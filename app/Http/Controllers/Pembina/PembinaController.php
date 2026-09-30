<?php

namespace App\Http\Controllers\Pembina;

use App\Http\Controllers\Controller;
use App\Models\Ekskul;
use App\Models\Faq;
use App\Models\Kegiatan;
use App\Models\LaporanBulanan;
use App\Models\Pelatih;
use App\Models\Pendaftaran;
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
        $ekskuls = $this->getEkskuls();
        $ekskul = $ekskuls->first();
        $ekskulIds = $ekskuls->pluck('id');

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

        $pelatihs = Pelatih::orderBy('nama')->get();

        $testimoniPendingCount = Testimoni::whereIn('ekskul_id', $ekskulIds)
            ->where('status', Testimoni::STATUS_PENDING)
            ->count();

        $faqPendingCount = Faq::whereIn('ekskul_id', $ekskulIds)
            ->where('status', Faq::STATUS_PENDING)
            ->count();

        return view('pembina.dashboard', compact('ekskul', 'ekskuls', 'pelatihs', 'anggota', 'anggotaAktifCount', 'pendaftaranPending', 'kegiatanMendatang', 'laporanDraft', 'testimoniPendingCount', 'faqPendingCount'));
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
            ->where('status', 'diterima')
            ->with(['siswa', 'siswa.kelas', 'ekskul']);

        if ($request->filled('cari')) {
            $cari = $request->input('cari');
            $query->whereHas('siswa', function ($q) use ($cari) {
                $q->where('nama', 'like', "%{$cari}%")
                    ->orWhere('nis', 'like', "%{$cari}%");
            });
        }

        $ekskulFilter = $request->input('ekskul');
        if ($ekskulFilter && $ekskuls->pluck('id')->contains($ekskulFilter)) {
            $query->where('ekskul_id', $ekskulFilter);
        }

        $anggota = $query->get();

        return view('pembina.anggota', compact('anggota', 'ekskuls'));
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

        $query = Pendaftaran::whereIn('ekskul_id', $ekskulIds)
            ->with(['siswa', 'siswa.kelas', 'ekskul'])
            ->latest('tanggal_daftar');

        $status = $request->input('status');
        if (in_array($status, ['pending', 'diterima', 'ditolak', 'nonaktif', 'keluar'])) {
            $query->where('status', $status);
        }

        if ($request->filled('cari')) {
            $cari = $request->input('cari');
            $query->whereHas('siswa', function ($q) use ($cari) {
                $q->where('nama', 'like', "%{$cari}%")
                    ->orWhere('nis', 'like', "%{$cari}%");
            });
        }

        $pendaftarans = $query->get();

        $grouped = $pendaftarans->groupBy('status');

        return view('pembina.pendaftaran', compact('pendaftarans', 'ekskuls', 'grouped'));
    }

    public function laporan()
    {
        $ekskuls = $this->getEkskuls();
        $ekskulIds = $ekskuls->pluck('id');

        $laporans = LaporanBulanan::whereIn('ekskul_id', $ekskulIds)
            ->where('status', '!=', 'draft')
            ->with('ekskul')
            ->latest('bulan')
            ->get();

        $kegiatans = Kegiatan::whereIn('ekskul_id', $ekskulIds)
            ->withCount(['presensis as hadir_count' => function ($q) {
                $q->where('status', 'hadir');
            }])
            ->withCount(['presensis as izin_count' => function ($q) {
                $q->where('status', 'izin');
            }])
            ->withCount(['presensis as sakit_count' => function ($q) {
                $q->where('status', 'sakit');
            }])
            ->withCount(['presensis as alpha_count' => function ($q) {
                $q->where('status', 'alpha');
            }])
            ->withCount('presensis as total_count')
            ->orderBy('tanggal_kegiatan', 'desc')
            ->get();

        return view('pembina.laporan', compact('laporans', 'kegiatans', 'ekskuls'));
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

    public function presensi()
    {
        $ekskuls = $this->getEkskuls();
        $ekskulIds = $ekskuls->pluck('id');

        $kegiatans = Kegiatan::whereIn('ekskul_id', $ekskulIds)
            ->with(['presensis.pendaftaran.siswa'])
            ->orderBy('tanggal_kegiatan', 'desc')
            ->get();

        return view('pembina.presensi', compact('kegiatans'));
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
            'jenis_kelamin' => ['required', 'in:laki-laki,perempuan'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore(auth()->id())],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'agama' => ['nullable', 'string', 'max:50'],
            'no_telp' => ['nullable', 'string', 'max:25'],
            'alamat' => ['nullable', 'string'],
            'medsos' => ['nullable', 'string', 'max:255'],
        ], [
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_).',
            'username.unique' => 'Username sudah digunakan akun lain.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah dipakai akun lain.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Format foto harus berupa JPG, PNG, atau WebP.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $user = auth()->user();

        DB::transaction(function () use ($pembina, $user, $validated, $request) {
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                if ($pembina->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($pembina->foto)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($pembina->foto);
                }
                $fotoPath = $request->file('foto')->store('profile-photos', 'public');
            }

            $updateData = [
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tempat_lahir' => $validated['tempat_lahir'] ?? $pembina->tempat_lahir,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? $pembina->tanggal_lahir,
                'agama' => $validated['agama'] ?? $pembina->agama,
                'email' => $validated['email'] ?? $pembina->email,
                'no_telp' => $validated['no_telp'] ?? $pembina->no_telp,
                'alamat' => $validated['alamat'] ?? $pembina->alamat,
                'medsos' => $validated['medsos'] ?? $pembina->medsos,
            ];

            if ($fotoPath) {
                $updateData['foto'] = $fotoPath;
            }

            $pembina->update($updateData);

            $userUpdates = [];
            if (!empty($validated['username']) && $validated['username'] !== $user->username) {
                $userUpdates['username'] = $validated['username'];
            }
            if (filled($validated['email']) && $validated['email'] !== $user->email) {
                $userUpdates['email'] = $validated['email'];
                $userUpdates['email_verified_at'] = null;
            }
            if (!empty($userUpdates)) {
                $user->update($userUpdates);
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
