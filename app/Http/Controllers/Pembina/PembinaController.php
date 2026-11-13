<?php

namespace App\Http\Controllers\Pembina;

use App\Http\Controllers\Controller;
use App\Models\Ekskul;
use App\Models\Faq;
use App\Models\Kelas;
use App\Models\Kegiatan;
use App\Models\LaporanBulanan;
use App\Models\Pelatih;
use App\Models\PembinaProfileHistory;
use App\Models\Pendaftaran;
use App\Models\Pengurus;
use App\Models\Presensi;
use App\Models\RiwayatJabatan;
use App\Models\Siswa;
use App\Models\Testimoni;
use App\Services\ArsipEkskulService;
use App\Services\NotifikasiService;
use App\Services\RekapAbsensiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PembinaController extends Controller
{
    /**
     * Semua ekskul yang dibina oleh pembina yang sedang login,
     * atau seluruh ekskul jika diakses oleh Kesiswaan / Admin.
     */
    private function getEkskuls(): Collection
    {
        $user = auth()->user();

        if ($user && in_array($user->role, ['kesiswaan', 'admin'], true)) {
            return Ekskul::orderBy('nama_ekskul')->get();
        }

        $pembina = $user?->pembina;

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

        $selectedEkskul = $request->input('ekskul');

        // Jika hanya membina 1 ekskul, otomatis pilih ekskul tersebut
        if (! $selectedEkskul && $ekskuls->count() === 1) {
            $selectedEkskul = $ekskuls->first()->id;
        }

        // TAMPILKAN KATALOG KARTU EKSKUL JIKA BELUM MEMILIH EKSKUL
        if (! $selectedEkskul || ! $ekskulIds->contains((int) $selectedEkskul)) {
            $ekskulCards = $ekskuls->map(function (Ekskul $e) {
                $anggotaCount = Pendaftaran::where('ekskul_id', $e->id)
                    ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
                    ->count();
                $ketua = $e->ketua();

                return (object) [
                    'ekskul' => $e,
                    'totalAnggota' => $anggotaCount,
                    'ketua' => $ketua,
                ];
            });

            return view('pembina.anggota', [
                'ekskuls' => $ekskuls,
                'selectedEkskul' => null,
                'ekskulCards' => $ekskulCards,
            ]);
        }

        $selectedEkskul = (int) $selectedEkskul;

        $query = Pendaftaran::where('ekskul_id', $selectedEkskul)
            ->with(['siswa.kelas', 'ekskul'])
            ->withCount([
                'presensis as hadir_count' => fn ($q) => $q->where('status', Presensi::STATUS_HADIR),
                'presensis as total_presensi',
            ]);

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

        // Arsip sengaja disembunyikan sampai filter diaktifkan. Total hanya dipakai
        // untuk label filter, data detail baru dirakit saat filter menyala.
        $tampilArsip = $request->boolean('arsip');

        $ekskulArsip = $selectedEkskul && $ekskulIds->contains((int) $selectedEkskul)
            ? $ekskuls->where('id', $selectedEkskul)->values()
            : $ekskuls;

        $arsipService = app(ArsipEkskulService::class);
        $arsipTotal = $arsipService->hitungTotal($ekskulArsip);

        $arsip = $tampilArsip
            ? $arsipService->arsip($ekskulArsip)
            : ['ketuaSelesai' => collect(), 'anggotaLulus' => collect(), 'total' => $arsipTotal];

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
            'cari',
            'tampilArsip',
            'arsip',
            'arsipTotal'
        ));
    }

    /**
     * Koreksi tanggal periode ketua pada arsip (mis. periode yang dicatat
     * otomatis tanggalnya kurang tepat).
     */
    public function updatePeriodeKetua(Request $request, RiwayatJabatan $riwayat)
    {
        $ekskulIds = $this->getEkskuls()->pluck('id');
        abort_unless($ekskulIds->contains($riwayat->ekskul_id), 403);

        $validated = $request->validate([
            'mulai' => ['required', 'date'],
            'selesai' => ['nullable', 'date', 'after_or_equal:mulai'],
        ], [
            'mulai.required' => 'Tanggal mulai periode wajib diisi.',
            'mulai.date' => 'Tanggal mulai periode tidak valid.',
            'selesai.date' => 'Tanggal selesai periode tidak valid.',
            'selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
        ]);

        $riwayat->update([
            'mulai' => $validated['mulai'],
            'selesai' => $validated['selesai'] ?: null,
            'alasan_selesai' => $validated['selesai'] ? ($riwayat->alasan_selesai ?: RiwayatJabatan::ALASAN_DIEDIT) : null,
        ]);

        return back()->with('success', 'Periode jabatan Ketua Ekskul '.$riwayat->ekskul->nama_ekskul.' atas nama '.$riwayat->siswa->nama.' berhasil diperbarui.');
    }

    public function pilihKetuaPage(Request $request, Ekskul $ekskul)
    {
        $ekskulIds = $this->getEkskuls()->pluck('id');
        abort_unless($ekskulIds->contains($ekskul->id), 403);

        $currentKetua = $ekskul->ketua();
        $cari = trim((string) $request->input('cari'));
        $kelasId = $request->integer('kelas_id') ?: null;

        $siswas = Siswa::query()
            ->with(['kelas', 'user'])
            ->where('status', 'aktif')
            ->when($cari !== '', fn ($query) => $query->where(fn ($sub) => $sub
                ->where('nama', 'like', "%{$cari}%")
                ->orWhere('nis', 'like', "%{$cari}%")))
            ->when($kelasId, fn ($query) => $query->where('kelas_id', $kelasId))
            ->orderBy('nama')
            ->paginate(12)
            ->withQueryString();

        $siswas->getCollection()->each(function (Siswa $siswa) use ($ekskul, $currentKetua) {
            $leaderEkskul = $siswa->ekskulDipimpin();
            $siswa->is_current_ketua = $currentKetua?->id === $siswa->id;
            $siswa->ekskul_dipimpin_lain = $leaderEkskul && $leaderEkskul->id !== $ekskul->id ? $leaderEkskul : null;
            $siswa->punya_akun = $siswa->user !== null && $siswa->user->is_active;
        });

        $kelasOptions = Kelas::query()
            ->whereHas('tahunAjaran', fn ($query) => $query->where('status', 'aktif'))
            ->whereHas('siswas', fn ($query) => $query->where('status', 'aktif'))
            ->orderBy('nama')
            ->get();

        return view('pembina.pilih-ketua', compact('ekskul', 'siswas', 'kelasOptions', 'currentKetua', 'cari', 'kelasId'));
    }

    public function pilihKetua(Request $request, Ekskul $ekskul, Siswa $siswa)
    {
        $user = auth()->user();
        $isKesiswaan = $user && in_array($user->role, ['kesiswaan', 'admin'], true);
        $pembina = $user?->pembina;

        abort_unless($isKesiswaan || ($pembina && $ekskul->pembina_id === $pembina->id), 403);

        abort_unless($siswa->status === 'aktif', 422, 'Siswa ini tidak aktif.');

        if ($ekskul->ketua()?->id === $siswa->id) {
            return back()->with('info', "{$siswa->nama} sudah menjadi Ketua Ekskul {$ekskul->nama_ekskul}.");
        }

        // Cek apakah siswa ini sudah menjadi ketua di ekskul lain
        $ekskulDipimpin = $siswa->ekskulDipimpin();
        $isKetuaDiLain = $ekskulDipimpin && $ekskulDipimpin->id !== $ekskul->id;

        if ($isKetuaDiLain) {
            return back()->with('error', "{$siswa->nama} saat ini sudah menjabat sebagai Ketua di ekskul {$ekskulDipimpin->nama_ekskul}. Satu siswa hanya dapat menjadi ketua pada 1 ekskul.");
        }

        $pendaftaran = DB::transaction(function () use ($ekskul, $siswa) {
            $arsip = app(ArsipEkskulService::class);

            $membership = Pendaftaran::query()
                ->where('ekskul_id', $ekskul->id)
                ->where('siswa_id', $siswa->id)
                ->latest('id')
                ->lockForUpdate()
                ->first();

            $membershipWasActive = $membership?->isActive() ?? false;
            if (! $membershipWasActive) {
                $candidate = $membership ?? new Pendaftaran;
                $candidate->ekskul_id = $ekskul->id;
                $candidate->setRelation('ekskul', $ekskul);

                if ($alasan = $siswa->alasanTidakDapatMenerima($candidate)) {
                    abort(422, $alasan);
                }

                if ($membership) {
                    $membership->update([
                        'tanggal_daftar' => today(),
                        'status' => Pendaftaran::STATUS_DITERIMA,
                        'alasan' => 'Ditetapkan sebagai Ketua oleh Pembina',
                    ]);
                    $membership = $membership->fresh();
                } else {
                    $membership = Pendaftaran::create([
                        'siswa_id' => $siswa->id,
                        'ekskul_id' => $ekskul->id,
                        'tanggal_daftar' => today(),
                        'status' => Pendaftaran::STATUS_DITERIMA,
                        'alasan' => 'Ditetapkan sebagai Ketua oleh Pembina',
                    ]);
                }
            }

            // Turunkan ketua lama di ekskul ini (jika ada) kembali menjadi siswa biasa
            $ketuaLamas = Siswa::where('jabatan', 'ketua')
                ->whereHas('pendaftarans', function ($q) use ($ekskul) {
                    $q->where('ekskul_id', $ekskul->id)
                        ->where('status', 'diterima');
                })
                ->get();

            foreach ($ketuaLamas as $lama) {
                $lama->update(['jabatan' => 'siswa']);

                Pengurus::query()
                    ->where('ekskul_id', $ekskul->id)
                    ->where('siswa_id', $lama->id)
                    ->where('jabatan', Pengurus::JABATAN_KETUA)
                    ->where('status', Pengurus::STATUS_AKTIF)
                    ->update(['status' => Pengurus::STATUS_SELESAI]);

                // Digantikan, bukan dicopot, sehingga arsipnya beralasan "diganti".
                $arsip->akhiriPeriodeKetua($ekskul, $lama, null, RiwayatJabatan::ALASAN_DIGANTI);
            }

            // Angkat siswa terpilih menjadi ketua
            $siswa->update(['jabatan' => 'ketua']);

            Pengurus::query()
                ->where('ekskul_id', $ekskul->id)
                ->where('jabatan', Pengurus::JABATAN_KETUA)
                ->where('status', Pengurus::STATUS_AKTIF)
                ->update(['status' => Pengurus::STATUS_SELESAI]);

            Pengurus::create([
                'ekskul_id' => $ekskul->id,
                'siswa_id' => $siswa->id,
                'jabatan' => Pengurus::JABATAN_KETUA,
                'status' => Pengurus::STATUS_AKTIF,
                'diusulkan_oleh' => 'pembina',
                'catatan' => 'Ditetapkan sebagai Ketua oleh Pembina',
            ]);

            // Buka periode baru agar riwayat ketua lama tersimpan sebagai arsip
            $arsip->mulaiPeriodeKetua($ekskul, $siswa);

            return ['pendaftaran' => $membership, 'anggota_baru' => ! $membershipWasActive];
        });

        if ($pendaftaran['anggota_baru']) {
            NotifikasiService::pendaftaranDiterima($pendaftaran['pendaftaran']);
        }
        \App\Models\Notifikasi::create([
            'siswa_id' => $siswa->id,
            'judul' => 'Penetapan Ketua Ekskul',
            'pesan' => "Anda ditetapkan sebagai Ketua Ekskul {$ekskul->nama_ekskul} oleh Pembina.",
            'tipe' => 'info',
        ]);

        return back()->with('success', "Berhasil menetapkan {$siswa->nama} ({$siswa->nis}) sebagai Ketua Ekskul {$ekskul->nama_ekskul}.");
    }

    public function copotKetua(Ekskul $ekskul, Siswa $siswa)
    {
        $user = auth()->user();
        $isKesiswaan = $user && in_array($user->role, ['kesiswaan', 'admin'], true);
        $pembina = $user?->pembina;

        abort_unless($isKesiswaan || ($pembina && $ekskul->pembina_id === $pembina->id), 403);

        if ($siswa->jabatan === 'ketua') {
            DB::transaction(function () use ($ekskul, $siswa) {
                $siswa->update(['jabatan' => 'siswa']);

                Pengurus::query()
                    ->where('ekskul_id', $ekskul->id)
                    ->where('siswa_id', $siswa->id)
                    ->where('jabatan', Pengurus::JABATAN_KETUA)
                    ->where('status', Pengurus::STATUS_AKTIF)
                    ->update(['status' => Pengurus::STATUS_SELESAI]);

                app(ArsipEkskulService::class)->akhiriPeriodeKetua($ekskul, $siswa);
            });
        }

        return back()->with('success', "Jabatan Ketua Ekskul {$ekskul->nama_ekskul} untuk {$siswa->nama} telah dicopot. Siswa kembali menjadi anggota biasa.");
    }

    public function pendaftaran(Request $request)
    {
        return redirect()->route('pembina.anggota');
    }

    public function pendaftaranLegacy(Request $request)
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

        // Ekskul yang belum punya ketua — pembina boleh ACC/tolak
        $ekskulTanpaKetua = $ekskuls->filter(fn ($e) => $e->ketua() === null)->pluck('id');

        return view('pembina.pendaftaran', [
            'pendaftarans' => $pendaftarans,
            'ekskuls' => $ekskuls,
            'grouped' => $baseCounts,
            'sort' => $sort,
            'direction' => $direction,
            'ekskulTanpaKetua' => $ekskulTanpaKetua,
        ]);
    }

    public function updatePendaftaran(Request $request, Pendaftaran $pendaftaran)
    {
        $ekskuls = $this->getEkskuls();
        $ekskulIds = $ekskuls->pluck('id');
        abort_unless($ekskulIds->contains($pendaftaran->ekskul_id), 403);

        // Pembina hanya boleh ACC/tolak jika ekskul belum punya ketua, namun Kesiswaan berwenang kapan saja
        $isKesiswaan = in_array(auth()->user()->role, ['kesiswaan', 'admin'], true);
        $ekskul = $ekskuls->firstWhere('id', $pendaftaran->ekskul_id);
        if (! $isKesiswaan && $ekskul && $ekskul->ketua() !== null) {
            return back()->with('error', 'Ekskul ini sudah memiliki ketua. Pendaftaran hanya bisa diproses oleh ketua ekskul.');
        }

        if ($pendaftaran->status !== Pendaftaran::STATUS_PENDING) {
            return back()->with('error', 'Hanya pendaftaran berstatus pending yang dapat diproses.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:diterima,ditolak'],
        ]);

        DB::transaction(function () use ($pendaftaran, $validated) {
            $pendaftaran = Pendaftaran::query()->lockForUpdate()->findOrFail($pendaftaran->id);
            Siswa::query()->lockForUpdate()->findOrFail($pendaftaran->siswa_id);

            abort_unless($pendaftaran->status === Pendaftaran::STATUS_PENDING, 422, 'Pendaftaran ini sudah diproses.');

            if ($validated['status'] === Pendaftaran::STATUS_DITERIMA) {
                $alasan = $pendaftaran->siswa->alasanTidakDapatMenerima($pendaftaran);
                abort_if($alasan !== null, 422, $alasan);
            }

            $pendaftaran->update($validated);

            if ($validated['status'] === Pendaftaran::STATUS_DITERIMA) {
                if ($pendaftaran->siswa->jabatan !== 'ketua') {
                    $pendaftaran->siswa->update(['jabatan' => 'anggota']);
                }
                NotifikasiService::pendaftaranDiterima($pendaftaran);
            } else {
                NotifikasiService::pendaftaranDitolak($pendaftaran);
            }
        });

        $statusLabel = $validated['status'] === 'diterima' ? 'diterima' : 'ditolak';

        return back()->with('success', "Pendaftaran {$pendaftaran->siswa->nama} berhasil {$statusLabel}.");
    }

    public function laporan(Request $request)
    {
        $ekskuls = $this->getEkskuls();
        $ekskulIds = $ekskuls->pluck('id');

        $selectedEkskulId = $request->input('ekskul');
        if (! $selectedEkskulId && $ekskuls->count() === 1) {
            $selectedEkskulId = $ekskuls->first()?->id;
        }

        // TAMPILKAN KATALOG KARTU EKSKUL JIKA BELUM MEMILIH EKSKUL
        if (! $selectedEkskulId || ! $ekskulIds->contains((int) $selectedEkskulId)) {
            $ekskulCards = $ekskuls->map(function (Ekskul $e) {
                $totalLaporan = LaporanBulanan::where('ekskul_id', $e->id)->where('status', '!=', 'draft')->count();
                $menungguCount = LaporanBulanan::where('ekskul_id', $e->id)->where('status', LaporanBulanan::STATUS_MENUNGGU)->count();
                $disetujuiCount = LaporanBulanan::where('ekskul_id', $e->id)->where('status', LaporanBulanan::STATUS_DISETUJUI)->count();
                $laporanTerbaru = LaporanBulanan::where('ekskul_id', $e->id)->where('status', '!=', 'draft')->latest('bulan')->first();

                return (object) [
                    'ekskul' => $e,
                    'totalLaporan' => $totalLaporan,
                    'menungguCount' => $menungguCount,
                    'disetujuiCount' => $disetujuiCount,
                    'laporanTerbaru' => $laporanTerbaru,
                ];
            });

            return view('pembina.laporan', [
                'ekskuls' => $ekskuls,
                'activeEkskul' => null,
                'ekskulCards' => $ekskulCards,
            ]);
        }

        $activeEkskul = $ekskuls->firstWhere('id', (int) $selectedEkskulId);

        $allowedSorts = ['bulan', 'status'];
        $sort = in_array($request->input('sort'), $allowedSorts, true) ? $request->input('sort') : 'bulan';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

        $query = LaporanBulanan::where('status', '!=', 'draft')->with('ekskul');

        if ($activeEkskul) {
            $query->where('ekskul_id', $activeEkskul->id);
        } else {
            $query->whereIn('ekskul_id', $ekskulIds);
        }

        // Search: bulan atau materi
        if ($request->filled('cari')) {
            $cari = $request->input('cari');
            $query->where(function ($q) use ($cari) {
                $q->where('bulan', 'like', "%{$cari}%");
            });
        }

        // Filter status
        $statusFilter = $request->input('status');
        if (in_array($statusFilter, ['menunggu', 'disetujui', 'ditolak'], true)) {
            $query->where('status', $statusFilter);
        }

        $laporans = $query->orderBy($sort, $direction)->paginate(10)->withQueryString();

        $kegiatanQuery = Kegiatan::query();
        if ($activeEkskul) {
            $kegiatanQuery->where('ekskul_id', $activeEkskul->id);
        } else {
            $kegiatanQuery->whereIn('ekskul_id', $ekskulIds);
        }

        $kegiatans = $kegiatanQuery
            ->withCount(['presensis as hadir_count' => fn ($q) => $q->where('status', 'hadir')])
            ->withCount(['presensis as izin_count' => fn ($q) => $q->where('status', 'izin')])
            ->withCount(['presensis as sakit_count' => fn ($q) => $q->where('status', 'sakit')])
            ->withCount(['presensis as alpha_count' => fn ($q) => $q->where('status', 'alpha')])
            ->orderBy('tanggal_kegiatan', 'desc')
            ->paginate(10, ['*'], 'presensi_page')
            ->withQueryString();

        return view('pembina.laporan', compact('laporans', 'kegiatans', 'ekskuls', 'activeEkskul', 'selectedEkskulId', 'sort', 'direction'));
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

        $tahun = substr($laporanBulanan->bulan, 0, 4);
        $bulan = substr($laporanBulanan->bulan, 5, 2);

        $kegiatanQuery = Kegiatan::where('ekskul_id', $ekskul->id)
            ->whereYear('tanggal_kegiatan', $tahun)
            ->whereMonth('tanggal_kegiatan', $bulan)
            ->orderBy('tanggal_kegiatan');

        $rutinKegiatans = (clone $kegiatanQuery)->whereNull('jenis_kegiatan')->get();
        $eventKegiatans = (clone $kegiatanQuery)->whereNotNull('jenis_kegiatan')->get();

        $rekapData = app(\App\Services\RekapAbsensiService::class)->rekap($ekskul, $laporanBulanan->bulan);

        $pdf = Pdf::loadView('ketua.laporan-bulanan.pdf', array_merge($rekapData, [
            'laporan' => $laporanBulanan,
            'kelas' => $kelas,
            'rutinKegiatans' => $rutinKegiatans,
            'eventKegiatans' => $eventKegiatans,
        ]));
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

        $selectedEkskul = $request->input('ekskul');
        if (! $selectedEkskul && $ekskuls->count() === 1) {
            $selectedEkskul = $ekskuls->first()?->id;
        }

        // TAMPILKAN KATALOG KARTU EKSKUL JIKA BELUM MEMILIH EKSKUL
        if (! $selectedEkskul || ! $ekskulIds->contains((int) $selectedEkskul)) {
            $ekskulCards = $ekskuls->map(function (Ekskul $e) {
                $totalKegiatan = Kegiatan::where('ekskul_id', $e->id)->count();
                $kegiatanTerakhir = Kegiatan::where('ekskul_id', $e->id)->latest('tanggal_kegiatan')->first();
                $totalAnggota = Pendaftaran::where('ekskul_id', $e->id)
                    ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
                    ->count();

                return (object) [
                    'ekskul' => $e,
                    'totalKegiatan' => $totalKegiatan,
                    'kegiatanTerakhir' => $kegiatanTerakhir,
                    'totalAnggota' => $totalAnggota,
                ];
            });

            return view('pembina.presensi', [
                'ekskuls' => $ekskuls,
                'selectedEkskul' => null,
                'ekskulCards' => $ekskulCards,
            ]);
        }

        $selectedEkskul = (int) $selectedEkskul;
        $activeEkskul = $ekskuls->firstWhere('id', $selectedEkskul);

        $allowedSorts = ['tanggal_kegiatan', 'materi'];
        $sort = in_array($request->input('sort'), $allowedSorts, true) ? $request->input('sort') : 'tanggal_kegiatan';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

        $query = Kegiatan::where('ekskul_id', $selectedEkskul)
            ->with(['presensis.pendaftaran.siswa', 'ekskul']);

        if ($request->filled('cari')) {
            $cari = $request->input('cari');
            $query->where(function ($q) use ($cari) {
                $q->where('materi', 'like', "%{$cari}%")
                    ->orWhereHas('presensis.pendaftaran.siswa', fn ($s) => $s->where('nama', 'like', "%{$cari}%"));
            });
        }

        $kegiatans = $query->orderBy($sort, $direction)->paginate(10)->withQueryString();

        return view('pembina.presensi', compact('kegiatans', 'ekskuls', 'selectedEkskul', 'activeEkskul', 'sort', 'direction'));
    }

    /**
     * Susun data rekap absensi untuk ekskul yang dipilih (atau ekskul pertama
     * yang dibina bila tidak ada filter), dipakai bersama oleh halaman rekap
     * dan unduhan PDF.
     *
     * @return array{0: Collection<int, Ekskul>, 1: int, 2: array}
     */
    private function rekapData(Request $request): array
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

        return [$ekskuls, $ekskulId, $rekap];
    }

    public function rekap(Request $request)
    {
        $ekskuls = $this->getEkskuls();
        $ekskulIds = $ekskuls->pluck('id');

        $selectedEkskul = $request->input('ekskul');
        if (! $selectedEkskul && $ekskuls->count() === 1) {
            $selectedEkskul = $ekskuls->first()?->id;
        }

        // JIKA BELUM MEMILIH EKSKUL: TAMPILKAN KATALOG KARTU EKSKUL UNTUK REKAP
        if (! $selectedEkskul || ! $ekskulIds->contains((int) $selectedEkskul)) {
            $ekskulCards = $ekskuls->map(function (Ekskul $e) {
                $totalAnggota = Pendaftaran::where('ekskul_id', $e->id)
                    ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
                    ->count();
                $totalKegiatan = Kegiatan::where('ekskul_id', $e->id)->count();

                return (object) [
                    'ekskul' => $e,
                    'totalAnggota' => $totalAnggota,
                    'totalKegiatan' => $totalKegiatan,
                ];
            });

            return view('pembina.rekap', [
                'ekskuls' => $ekskuls,
                'ekskulId' => null,
                'ekskul' => null,
                'ekskulCards' => $ekskulCards,
                'bulan' => now()->format('Y-m'),
                'rows' => collect(),
                'kegiatans' => collect(),
                'eventKegiatans' => collect(),
                'availableMonths' => collect([now()->format('Y-m')]),
                'totalHadir' => 0,
                'totalIzin' => 0,
                'totalSakit' => 0,
                'totalAlpha' => 0,
                'pelatih' => null,
                'presensiPelatih' => [],
            ]);
        }

        [$ekskuls, $ekskulId, $rekap] = $this->rekapData($request);

        return view('pembina.rekap', array_merge($rekap, [
            'ekskuls' => $ekskuls,
            'ekskulId' => $ekskulId,
            'bulan' => $rekap['bulan'],
            'ekskulCards' => null,
        ]));
    }

    public function rekapPdf(Request $request)
    {
        [, , $rekap] = $this->rekapData($request);

        abort_if($rekap['ekskul'] === null, 404, 'Ekskul tidak ditemukan atau tidak anda bina.');

        $pdf = Pdf::loadView('ketua.presensi.rekap-pdf', $rekap);

        $filename = 'rekap-absensi-'.str_replace('/', '-', $rekap['bulan']).'-'.Str::slug($rekap['ekskul']->nama_ekskul ?? 'ekskul').'.pdf';

        return $pdf->download($filename);
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
        $oldUsername = $user->username;
        $oldEmail = $user->email;
        $oldFoto = $pembina->foto;
        $oldNoTelp = $pembina->no_telp;
        $newUsername = !empty($validated['username']) ? $validated['username'] : $oldUsername;
        $newEmail = $validated['email'] ?? null;
        $newNoTelp = $validated['no_telp'] ?? null;
        $fotoPath = $request->hasFile('foto')
            ? $request->file('foto')->store('profile-photos', 'public')
            : $oldFoto;

        $changedFields = [];

        DB::transaction(function () use ($pembina, $user, $validated, $oldUsername, $oldEmail, $oldFoto, $oldNoTelp, $newUsername, $newEmail, $newNoTelp, $fotoPath, &$changedFields) {
            $userUpdates = [];
            if (! empty($validated['username']) && $validated['username'] !== $user->username) {
                $userUpdates['username'] = $validated['username'];
            }
            if ($newEmail !== $oldEmail) {
                $userUpdates['email'] = $newEmail;
                $userUpdates['email_verified_at'] = null;
            }
            if (! empty($userUpdates)) {
                $user->update($userUpdates);
            }

            $changes = [
                'username' => [$oldUsername, $newUsername],
                'email' => [$oldEmail, $newEmail],
                'no_telp' => [$oldNoTelp, $newNoTelp],
                'foto' => [$oldFoto, $fotoPath],
            ];

            $pembina->update([
                'email' => $newEmail,
                'no_telp' => $newNoTelp,
                'foto' => $fotoPath,
            ]);

            foreach ($changes as $field => [$oldValue, $newValue]) {
                if ($oldValue !== $newValue) {
                    $changedFields[] = $field;
                    PembinaProfileHistory::create([
                        'pembina_id' => $pembina->id,
                        'changed_by_user_id' => $user->id,
                        'field' => $field,
                        'old_value' => $oldValue,
                        'new_value' => $newValue,
                    ]);
                }
            }

            if (!empty($changedFields)) {
                NotifikasiService::profilPembinaDiubah($pembina, $changedFields);
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

    public function anggotaShow(Pendaftaran $pendaftaran)
    {
        $ekskuls = $this->getEkskuls();
        abort_unless($ekskuls->pluck('id')->contains($pendaftaran->ekskul_id), 403);

        $pendaftaran->load([
            'siswa.kelas',
            'ekskul.pembina.user',
            'ekskul.pelatih',
            'siswa.pendaftarans.ekskul',
            'siswa.pengajuanKeluars.ekskul',
            'siswa.riwayatJabatans.ekskul',
        ]);

        // Hitung statistik kehadiran untuk ekskul ini
        $presensiStats = Presensi::where('pendaftaran_id', $pendaftaran->id)
            ->selectRaw('
                count(*) as total,
                sum(case when status = "hadir" then 1 else 0 end) as hadir,
                sum(case when status = "izin" then 1 else 0 end) as izin,
                sum(case when status = "sakit" then 1 else 0 end) as sakit,
                sum(case when status = "alpha" then 1 else 0 end) as alpha
            ')
            ->first();

        $totalPresensi = (int) ($presensiStats->total ?? 0);
        $hadir = (int) ($presensiStats->hadir ?? 0);
        $persentase = $totalPresensi > 0 ? round(($hadir / $totalPresensi) * 100, 1) : 0;

        return view('pembina.anggota.show', compact(
            'pendaftaran',
            'ekskuls',
            'totalPresensi',
            'hadir',
            'persentase'
        ));
    }

    public function anggotaByEkskul(Request $request, Ekskul $ekskul)
    {
        $ekskuls = $this->getEkskuls();
        abort_unless($ekskuls->pluck('id')->contains($ekskul->id), 403);

        // Redirect ke halaman anggota utama dengan ekskul sudah dipilih
        // agar semua filter/sort/search tetap konsisten di satu tempat
        return redirect()->route('pembina.anggota', array_merge(
            $request->only(['cari', 'sort', 'status_keanggotaan', 'jurusan', 'jenis_kelamin', 'tingkat', 'status_keaktifan']),
            ['ekskul' => $ekskul->id]
        ));
    }
}
