<?php

namespace App\Http\Controllers\Pembina;

use App\Http\Controllers\Controller;
use App\Models\Ekskul;
use App\Models\Faq;
use App\Models\Kegiatan;
use App\Models\LaporanBulanan;
use App\Models\Pelatih;
use App\Models\Pendaftaran;
use App\Models\PembinaProfileHistory;
use App\Models\Siswa;
use App\Models\Testimoni;
use App\Services\NotifikasiService;
use App\Services\RekapAbsensiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PembinaController extends Controller
{
    /**
     * Semua ekskul yang dibina oleh pembina yang sedang login.
     */
    private function getEkskuls(): Collection
    {
        $pembina = auth()->user()?->pembina;

        if (! $pembina) {
            return collect();
        }

        return $pembina->ekskuls()->get();
    }

    private function getEkskul()
    {
        return $this->getEkskuls()->first();
    }

    public function dashboard()
    {
        $pembina = auth()->user()?->pembina;
        $ekskuls = $this->getEkskuls();
        $ekskul = $ekskuls->first();
        $ekskulIds = $ekskuls->pluck('id');

        $ekskulsByBidang = $ekskuls->groupBy(fn ($e) => $e->bidang);

        $anggota = Pendaftaran::whereIn('ekskul_id', $ekskulIds)
            ->whereIn('status', ['diterima', 'nonaktif', 'keluar'])
            ->with(['siswa', 'siswa.kelas'])
            ->latest('tanggal_daftar')
            ->get();

        $anggotaAktifCount = $anggota->where('status', 'diterima')->count();

        $pendaftaranPending = Pendaftaran::whereIn('ekskul_id', $ekskulIds)
            ->where('status', 'pending')
            ->with(['siswa', 'siswa.kelas'])
            ->latest('tanggal_daftar')
            ->get();

        $kegiatanMendatang = Kegiatan::whereIn('ekskul_id', $ekskulIds)
            ->whereDate('tanggal_kegiatan', '>=', today())
            ->orderBy('tanggal_kegiatan', 'asc')
            ->get();

        $laporanDraft = LaporanBulanan::whereIn('ekskul_id', $ekskulIds)
            ->where('status', 'draft')
            ->latest('bulan')
            ->get();

        $pelatihs = Pelatih::where('status_verifikasi', Pelatih::VERIFIKASI_TERVERIFIKASI)->orderBy('nama')->get();

        $testimoniPendingCount = Testimoni::whereIn('ekskul_id', $ekskulIds)
            ->where('status', Testimoni::STATUS_PENDING)
            ->count();

        $faqPendingCount = Faq::whereIn('ekskul_id', $ekskulIds)
            ->where('status', Faq::STATUS_PENDING)
            ->count();

        return view('pembina.dashboard', compact(
            'pembina',
            'ekskul',
            'ekskuls',
            'ekskulsByBidang',
            'pelatihs',
            'anggota',
            'anggotaAktifCount',
            'pendaftaranPending',
            'kegiatanMendatang',
            'laporanDraft',
            'testimoniPendingCount',
            'faqPendingCount'
        ));
    }

    public function updatePelatih(Request $request, Ekskul $ekskul)
    {
        abort_unless($this->getEkskuls()->pluck('id')->contains($ekskul->id), 403);

        $validated = $request->validate([
            'pelatih_id' => ['nullable', 'exists:pelatihs,id'],
        ]);

        $ekskul->update(['pelatih_id' => $validated['pelatih_id'] ?? null]);

        $namaPelatih = $ekskul->pelatih?->nama ?? '-';

        return back()->with('success', "Pelatih {$ekskul->nama_ekskul} diperbarui: {$namaPelatih}.");
    }

    public function anggota(Request $request)
    {
        $ekskuls = $this->getEkskuls();
        $ekskulIds = $ekskuls->pluck('id');

        $query = Pendaftaran::whereIn('ekskul_id', $ekskulIds)
            ->with(['siswa.kelas', 'ekskul'])
            ->withCount([
                'presensis as hadir_count' => fn ($q) => $q->where('status', \App\Models\Presensi::STATUS_HADIR),
                'presensis as total_presensi',
            ]);

        // 1. Dropdown Filter Ekskul
        $selectedEkskul = $request->input('ekskul');
        if ($selectedEkskul && $selectedEkskul !== 'semua' && $ekskulIds->contains($selectedEkskul)) {
            $query->where('ekskul_id', $selectedEkskul);
        }

        // 2. Search: Nama siswa, NIS, email
        $cari = trim((string) $request->input('cari'));
        if ($cari !== '') {
            $query->whereHas('siswa', function ($q) use ($cari) {
                $q->where('nama', 'like', "%{$cari}%")
                    ->orWhere('nis', 'like', "%{$cari}%")
                    ->orWhere('email', 'like', "%{$cari}%");
            });
        }

        // 3. Filter Status Keanggotaan (Aktif vs Non-aktif)
        $statusKeanggotaan = $request->input('status_keanggotaan', 'aktif');
        if ($statusKeanggotaan === 'aktif') {
            $query->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN]);
        } elseif ($statusKeanggotaan === 'nonaktif') {
            $query->whereIn('status', [Pendaftaran::STATUS_NONAKTIF, Pendaftaran::STATUS_KELUAR]);
        } elseif ($statusKeanggotaan !== 'semua' && in_array($statusKeanggotaan, [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN, Pendaftaran::STATUS_NONAKTIF, Pendaftaran::STATUS_KELUAR])) {
            $query->where('status', $statusKeanggotaan);
        } else {
            $query->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN, Pendaftaran::STATUS_NONAKTIF, Pendaftaran::STATUS_KELUAR]);
        }

        // 4. Filter Jurusan
        $selectedJurusan = $request->input('jurusan');
        if ($selectedJurusan && $selectedJurusan !== 'semua') {
            $query->whereHas('siswa.kelas', fn ($k) => $k->where('jurusan', $selectedJurusan));
        }

        // 5. Filter Jenis Kelamin
        $selectedJenisKelamin = $request->input('jenis_kelamin');
        if ($selectedJenisKelamin && $selectedJenisKelamin !== 'semua') {
            $query->whereHas('siswa', fn ($s) => $s->where('jenis_kelamin', $selectedJenisKelamin));
        }

        // 6. Filter Tingkat Kelas (10, 11, 12 atau x, xi, xii)
        $selectedTingkat = $request->input('tingkat');
        if ($selectedTingkat && $selectedTingkat !== 'semua') {
            $tingkatRaw = strtolower((string) $selectedTingkat);
            $tVal = match ($tingkatRaw) {
                '10', 'x' => 'x',
                '11', 'xi' => 'xi',
                '12', 'xii' => 'xii',
                default => $tingkatRaw,
            };
            $query->whereHas('siswa.kelas', fn ($k) => $k->where('tingkat', $tVal));
        }

        $anggotaCollection = $query->get();

        // Kalkulasi Persentase Kehadiran & Status Keaktifan
        $anggotaCollection->each(function ($item) {
            $total = (int) $item->total_presensi;
            $hadir = (int) $item->hadir_count;
            $item->persentase_kehadiran = $total > 0 ? round(($hadir / $total) * 100, 1) : 0.0;

            if ($item->persentase_kehadiran >= 80) {
                $item->status_keaktifan = 'sangat_aktif';
                $item->label_keaktifan = 'Sangat Aktif';
            } elseif ($item->persentase_kehadiran >= 50) {
                $item->status_keaktifan = 'cukup_aktif';
                $item->label_keaktifan = 'Cukup Aktif';
            } elseif ($item->persentase_kehadiran > 0) {
                $item->status_keaktifan = 'kurang_aktif';
                $item->label_keaktifan = 'Kurang Aktif';
            } else {
                $item->status_keaktifan = 'pasif';
                $item->label_keaktifan = 'Belum Ada Presensi';
            }
        });

        // 7. Filter Status Keaktifan
        $selectedStatusKeaktifan = $request->input('status_keaktifan');
        if ($selectedStatusKeaktifan && $selectedStatusKeaktifan !== 'semua') {
            $anggotaCollection = $anggotaCollection->filter(function ($item) use ($selectedStatusKeaktifan) {
                if ($selectedStatusKeaktifan === 'peringatan') {
                    return $item->status === Pendaftaran::STATUS_PERINGATAN;
                }
                return $item->status_keaktifan === $selectedStatusKeaktifan;
            });
        }

        // 8. Sorting
        $sort = $request->input('sort', 'nama_asc');
        switch ($sort) {
            case 'nama_desc':
                $anggota = $anggotaCollection->sortByDesc(fn ($i) => strtolower($i->siswa?->nama ?? ''), SORT_NATURAL)->values();
                break;
            case 'tanggal_daftar_desc':
                $anggota = $anggotaCollection->sortByDesc('tanggal_daftar')->values();
                break;
            case 'tanggal_daftar_asc':
                $anggota = $anggotaCollection->sortBy('tanggal_daftar')->values();
                break;
            case 'kehadiran_desc':
                $anggota = $anggotaCollection->sortByDesc('persentase_kehadiran')->values();
                break;
            case 'kehadiran_asc':
                $anggota = $anggotaCollection->sortBy('persentase_kehadiran')->values();
                break;
            case 'nama_asc':
            default:
                $sort = 'nama_asc';
                $anggota = $anggotaCollection->sortBy(fn ($i) => strtolower($i->siswa?->nama ?? ''), SORT_NATURAL)->values();
                break;
        }

        $jurusans = config('kelas.jurusan', []);
        $tingkats = config('kelas.tingkat', []);

        return view('pembina.anggota', compact(
            'anggota',
            'ekskuls',
            'jurusans',
            'tingkats',
            'sort',
            'statusKeanggotaan',
            'selectedEkskul',
            'selectedJurusan',
            'selectedJenisKelamin',
            'selectedTingkat',
            'selectedStatusKeaktifan',
            'cari'
        ));
    }

    public function pilihKetua(Request $request, Ekskul $ekskul, Siswa $siswa)
    {
        $pembina = auth()->user()?->pembina;
        abort_unless($pembina && $ekskul->pembina_id === $pembina->id, 403);

        // Pastikan siswa terdaftar dengan status diterima di ekskul ini
        $pendaftaran = Pendaftaran::where('ekskul_id', $ekskul->id)
            ->where('siswa_id', $siswa->id)
            ->where('status', 'diterima')
            ->first();

        if (! $pendaftaran) {
            return back()->with('error', 'Siswa yang dipilih bukan anggota aktif di ekskul '.$ekskul->nama_ekskul.'.');
        }

        // Cek apakah siswa ini sudah menjadi ketua di ekskul lain
        $isKetuaDiLain = Siswa::where('id', $siswa->id)
            ->where('jabatan', 'ketua')
            ->whereHas('pendaftarans', function ($q) use ($ekskul) {
                $q->where('ekskul_id', '!=', $ekskul->id)
                  ->where('status', 'diterima');
            })
            ->exists();

        if ($isKetuaDiLain) {
            return back()->with('error', "{$siswa->nama} saat ini sudah menjabat sebagai Ketua di ekskul lain. Satu siswa hanya dapat menjadi ketua pada 1 ekskul.");
        }

        DB::transaction(function () use ($ekskul, $siswa) {
            // Turunkan ketua lama di ekskul ini (jika ada) kembali menjadi siswa biasa
            $ketuaLamas = Siswa::where('jabatan', 'ketua')
                ->whereHas('pendaftarans', function ($q) use ($ekskul) {
                    $q->where('ekskul_id', $ekskul->id)
                      ->where('status', 'diterima');
                })
                ->get();

            foreach ($ketuaLamas as $lama) {
                $lama->update(['jabatan' => 'siswa']);
            }

            // Angkat siswa terpilih menjadi ketua
            $siswa->update(['jabatan' => 'ketua']);
        });

        return back()->with('success', "Berhasil menetapkan {$siswa->nama} ({$siswa->nis}) sebagai Ketua Ekskul {$ekskul->nama_ekskul}.");
    }

    public function copotKetua(Ekskul $ekskul, Siswa $siswa)
    {
        $pembina = auth()->user()?->pembina;
        abort_unless($pembina && $ekskul->pembina_id === $pembina->id, 403);

        if ($siswa->jabatan === 'ketua') {
            $siswa->update(['jabatan' => 'siswa']);
        }

        return back()->with('success', "Jabatan Ketua Ekskul {$ekskul->nama_ekskul} untuk {$siswa->nama} telah dicopot. Siswa kembali menjadi anggota biasa.");
    }

    public function pendaftaran(Request $request)
    {
        $ekskuls = $this->getEkskuls();
        $ekskulIds = $ekskuls->pluck('id');

        $baseCounts = Pendaftaran::whereIn('ekskul_id', $ekskulIds)->get()->groupBy('status');

        $sort = in_array($request->input('sort'), ['nama', 'tanggal_daftar', 'status'], true) ? $request->input('sort') : 'tanggal_daftar';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

        $query = Pendaftaran::whereIn('ekskul_id', $ekskulIds)
            ->with(['siswa', 'siswa.kelas', 'ekskul']);

        $status = $request->input('status');
        if (in_array($status, ['pending', 'diterima', 'ditolak', 'nonaktif', 'keluar'], true)) {
            $query->where('status', $status);
        }

        if ($request->filled('ekskul') && $ekskulIds->contains($request->input('ekskul'))) {
            $query->where('ekskul_id', $request->input('ekskul'));
        }

        if ($request->filled('cari')) {
            $cari = $request->input('cari');
            $query->whereHas('siswa', function ($q) use ($cari) {
                $q->where('nama', 'like', "%{$cari}%")
                    ->orWhere('nis', 'like', "%{$cari}%");
            });
        }

        if ($sort === 'nama') {
            $query->join('siswas', 'siswas.id', '=', 'pendaftarans.siswa_id')
                ->select('pendaftarans.*')
                ->orderBy('siswas.nama', $direction);
        } else {
            $query->orderBy($sort, $direction);
        }

        $pendaftarans = $query->paginate(15)->withQueryString();

        return view('pembina.pendaftaran', [
            'pendaftarans' => $pendaftarans,
            'ekskuls' => $ekskuls,
            'grouped' => $baseCounts,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function laporan(Request $request)
    {
        $ekskuls = $this->getEkskuls();
        $ekskulIds = $ekskuls->pluck('id');

        $allowedSorts = ['bulan', 'status'];
        $sort      = in_array($request->input('sort'), $allowedSorts, true) ? $request->input('sort') : 'bulan';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

        $query = LaporanBulanan::whereIn('ekskul_id', $ekskulIds)
            ->where('status', '!=', 'draft')
            ->with('ekskul');

        // Search: bulan atau ekskul
        if ($request->filled('cari')) {
            $cari = $request->input('cari');
            $query->where(function ($q) use ($cari) {
                $q->where('bulan', 'like', "%{$cari}%")
                  ->orWhereHas('ekskul', fn ($e) => $e->where('nama_ekskul', 'like', "%{$cari}%"));
            });
        }

        // Filter status
        $statusFilter = $request->input('status');
        if (in_array($statusFilter, ['menunggu', 'disetujui', 'ditolak'], true)) {
            $query->where('status', $statusFilter);
        }

        // Filter ekskul
        if ($request->filled('ekskul') && $ekskulIds->contains($request->input('ekskul'))) {
            $query->where('ekskul_id', $request->input('ekskul'));
        }

        $laporans = $query->orderBy($sort, $direction)->paginate(10)->withQueryString();

        $kegiatans = \App\Models\Kegiatan::whereIn('ekskul_id', $ekskulIds)
            ->withCount(['presensis as hadir_count' => fn ($q) => $q->where('status', 'hadir')])
            ->withCount(['presensis as izin_count'  => fn ($q) => $q->where('status', 'izin')])
            ->withCount(['presensis as sakit_count' => fn ($q) => $q->where('status', 'sakit')])
            ->withCount(['presensis as alpha_count' => fn ($q) => $q->where('status', 'alpha')])
            ->orderBy('tanggal_kegiatan', 'desc')
            ->paginate(10, ['*'], 'presensi_page')
            ->withQueryString();

        return view('pembina.laporan', compact('laporans', 'kegiatans', 'ekskuls', 'sort', 'direction'));
    }

    public function laporanShow(LaporanBulanan $laporanBulanan)
    {
        abort_unless($this->getEkskuls()->pluck('id')->contains($laporanBulanan->ekskul_id), 403);
        abort_if($laporanBulanan->status === 'draft', 404);

        $laporanBulanan->load('ekskul');

        return view('pembina.laporan-show', ['laporan' => $laporanBulanan]);
    }

    public function laporanDownload(LaporanBulanan $laporanBulanan)
    {
        abort_unless($this->getEkskuls()->pluck('id')->contains($laporanBulanan->ekskul_id), 403);
        abort_if($laporanBulanan->status === 'draft', 404);

        $ekskul = $laporanBulanan->ekskul;
        $kelas = $this->generateKelas($ekskul);
        $pdf = Pdf::loadView('ketua.laporan-bulanan.pdf', ['laporan' => $laporanBulanan, 'kelas' => $kelas]);
        $filename = 'laporan-'.str_replace('/', '-', $laporanBulanan->bulan).'-'.($ekskul->nama_ekskul ?? 'ekskul').'.pdf';

        return $pdf->download($filename);
    }

    public function laporanApprove(LaporanBulanan $laporanBulanan)
    {
        abort_unless($this->getEkskuls()->pluck('id')->contains($laporanBulanan->ekskul_id), 403);
        abort_if($laporanBulanan->status !== LaporanBulanan::STATUS_MENUNGGU, 403, 'Laporan hanya bisa disetujui saat berstatus menunggu.');

        $laporanBulanan->update([
            'status' => LaporanBulanan::STATUS_DISETUJUI,
            'catatan_pembina' => null,
        ]);

        NotifikasiService::laporanDitinjau($laporanBulanan, LaporanBulanan::STATUS_DISETUJUI);

        return redirect()->route('pembina.laporan.show', $laporanBulanan)
            ->with('success', 'Laporan disetujui.');
    }

    public function laporanReject(Request $request, LaporanBulanan $laporanBulanan)
    {
        abort_unless($this->getEkskuls()->pluck('id')->contains($laporanBulanan->ekskul_id), 403);
        abort_if($laporanBulanan->status !== LaporanBulanan::STATUS_MENUNGGU, 403, 'Laporan hanya bisa ditolak saat berstatus menunggu.');

        $validated = $request->validate([
            'catatan_pembina' => 'nullable|string',
        ]);

        $laporanBulanan->update([
            'status' => LaporanBulanan::STATUS_DITOLAK,
            'catatan_pembina' => $validated['catatan_pembina'] ?? null,
        ]);

        NotifikasiService::laporanDitinjau($laporanBulanan, LaporanBulanan::STATUS_DITOLAK);

        return redirect()->route('pembina.laporan.show', $laporanBulanan)
            ->with('success', 'Laporan ditolak.');
    }

    public function presensi(Request $request)
    {
        $ekskuls = $this->getEkskuls();
        $ekskulIds = $ekskuls->pluck('id');

        $allowedSorts = ['tanggal_kegiatan', 'materi'];
        $sort = in_array($request->input('sort'), $allowedSorts, true) ? $request->input('sort') : 'tanggal_kegiatan';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

        $query = Kegiatan::whereIn('ekskul_id', $ekskulIds)
            ->with(['presensis.pendaftaran.siswa', 'ekskul']);

        if ($request->filled('cari')) {
            $cari = $request->input('cari');
            $query->where(function ($q) use ($cari) {
                $q->where('materi', 'like', "%{$cari}%")
                  ->orWhereHas('presensis.pendaftaran.siswa', fn ($s) => $s->where('nama', 'like', "%{$cari}%"));
            });
        }

        if ($request->filled('ekskul') && $request->input('ekskul') !== 'semua' && $ekskulIds->contains($request->input('ekskul'))) {
            $query->where('ekskul_id', $request->input('ekskul'));
        }

        $kegiatans = $query->orderBy($sort, $direction)->paginate(10)->withQueryString();

        return view('pembina.presensi', compact('kegiatans', 'ekskuls', 'sort', 'direction'));
    }

    public function rekap(Request $request)
    {
        $ekskuls = $this->getEkskuls();
        $ekskulIds = $ekskuls->pluck('id');

        $service = app(RekapAbsensiService::class);
        $bulan = $service->normalizeBulan($request->input('bulan'));

        $ekskulFilter = $request->input('ekskul');
        $ekskulId = $ekskulFilter && $ekskulIds->contains($ekskulFilter)
            ? (int) $ekskulFilter
            : (int) $ekskuls->first()?->id;

        $ekskul = $ekskuls->firstWhere('id', $ekskulId);

        $rekap = $ekskul
            ? $service->rekap($ekskul, $bulan)
            : [
                'ekskul' => null,
                'bulan' => $bulan,
                'kegiatans' => collect(),
                'eventKegiatans' => collect(),
                'rows' => collect(),
                'pelatih' => null,
                'presensiPelatih' => [],
                'totalHadir' => 0,
                'totalIzin' => 0,
                'totalSakit' => 0,
                'totalAlpha' => 0,
                'availableMonths' => collect([now()->format('Y-m')]),
            ];

        return view('pembina.rekap', array_merge($rekap, [
            'ekskuls' => $ekskuls,
            'ekskulId' => $ekskulId,
            'bulan' => $bulan,
        ]));
    }

    public function profile()
    {
        return view('pembina.profile');
    }

    public function updateProfile(Request $request)
    {
        $pembina = auth()->user()?->pembina;

        abort_unless($pembina, 404);

        $validated = $request->validate([
            'username' => ['nullable', 'string', 'min:3', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore(auth()->id())],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore(auth()->id())],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'no_telp' => ['nullable', 'string', 'max:25'],
        ], [
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_).',
            'username.unique' => 'Username sudah digunakan akun lain.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah dipakai akun lain.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Format foto harus berupa JPG, PNG, atau WebP.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $user = auth()->user();
        $oldEmail = $user->email;
        $oldFoto = $pembina->foto;
        $newEmail = $validated['email'] ?? null;
        $newNoTelp = $validated['no_telp'] ?? null;
        $fotoPath = $request->hasFile('foto')
            ? $request->file('foto')->store('profile-photos', 'public')
            : $oldFoto;

        DB::transaction(function () use ($pembina, $user, $validated, $oldEmail, $oldFoto, $newEmail, $newNoTelp, $fotoPath) {
            $userUpdates = [];
            if (!empty($validated['username']) && $validated['username'] !== $user->username) {
                $userUpdates['username'] = $validated['username'];
            }
            if ($newEmail !== $oldEmail) {
                $userUpdates['email'] = $newEmail;
                $userUpdates['email_verified_at'] = null;
            }
            if (!empty($userUpdates)) {
                $user->update($userUpdates);
            }

            $changes = [
                'email' => [$oldEmail, $newEmail],
                'no_telp' => [$pembina->no_telp, $newNoTelp],
                'foto' => [$oldFoto, $fotoPath],
            ];

            $pembina->update([
                'email' => $newEmail,
                'no_telp' => $newNoTelp,
                'foto' => $fotoPath,
            ]);

            foreach ($changes as $field => [$oldValue, $newValue]) {
                if ($oldValue !== $newValue) {
                    PembinaProfileHistory::create([
                        'pembina_id' => $pembina->id,
                        'changed_by_user_id' => $user->id,
                        'field' => $field,
                        'old_value' => $oldValue,
                        'new_value' => $newValue,
                    ]);
                }
            }

            if ($pembina->isProfileComplete()) {
                $user->update(['onboarding_completed_at' => $user->onboarding_completed_at ?? now()]);
            }
        });

        return redirect()->route('pembina.profile')->with('success', 'Profil berhasil diperbarui.');
    }

    private function generateKelas(Ekskul $ekskul)
    {
        $tingkats = $ekskul->pendaftarans()
            ->where('status', 'diterima')
            ->with('siswa.kelas')
            ->get()
            ->pluck('siswa.kelas.tingkat')
            ->unique()
            ->sort()
            ->values();

        if ($tingkats->isEmpty()) {
            return '-';
        }

        $labels = $tingkats->map(fn ($t) => config("kelas.tingkat.{$t}"))->values();

        if ($labels->count() === 1) {
            return $labels->first();
        }

        if ($labels->count() === 2) {
            return $labels->join(' & ');
        }

        $last = $labels->pop();

        return $labels->implode(', ').', dan '.$last;
    }
}
