<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Kelas;
use App\Models\Pendaftaran;
use App\Models\RiwayatPendaftaran;
use App\Models\Siswa;
use App\Services\ArsipEkskulService;
use App\Services\NotifikasiService;
use App\Support\TableKit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AnggotaController extends Controller
{
    use KetuaEkskul;

    public function index(Request $request)
    {
        $ekskul = $this->ekskul();
        $base = Pendaftaran::where('ekskul_id', $ekskul->id)
            ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_NONAKTIF, Pendaftaran::STATUS_PERINGATAN, Pendaftaran::STATUS_KELUAR]);

        $totalAnggotas = (clone $base)->count();

        // Tanpa filter status, hanya anggota aktif yang ditampilkan.
        $statusDipilih = $request->input('status');
        $scopeAktif = ! $request->filled('status') || $statusDipilih === 'semua';

        // Angka legenda dihitung dari seluruh anggota agar tidak ikut terfilter.
        $peringatanCount = (clone $base)->where('status', Pendaftaran::STATUS_PERINGATAN)->count();
        $nonaktifCount = (clone $base)->where('status', Pendaftaran::STATUS_NONAKTIF)->count();
        $keluarCount = (clone $base)->where('status', Pendaftaran::STATUS_KELUAR)->count();

        $query = (clone $base)
            ->when($scopeAktif, fn ($query) => $query->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN]))
            ->when($request->filled('cari'), function ($query) use ($request) {
                $cari = $request->input('cari');
                $query->whereHas('siswa', fn ($s) => $s->where('nama', 'like', "%{$cari}%")->orWhere('nis', 'like', "%{$cari}%"));
            })
            ->when($request->filled('status') && $statusDipilih !== 'semua', fn ($query) => $query->where('status', $statusDipilih));

        [$sort, $direction] = TableKit::sort(['nama', 'nis', 'status', 'tanggal_daftar'], 'tanggal_daftar', 'desc');

        $anggotas = (clone $query)
            ->with('siswa.kelas')
            ->orderBy($sort === 'nama' || $sort === 'nis' ? Siswa::select($sort)->whereColumn('siswas.id', 'pendaftarans.siswa_id') : $sort, $direction)
            ->paginate(10)
            ->withQueryString();

        // Arsip hanya dirakit ketika filter diaktifkan; label filter memakai
        // hitungan ringan yang tidak memuat detail.
        $tampilArsip = $request->boolean('arsip');
        $arsipService = app(ArsipEkskulService::class);
        $arsipTotal = $arsipService->hitungTotal($ekskul);

        $arsip = $tampilArsip
            ? $arsipService->arsip($ekskul)
            : ['ketuaSelesai' => collect(), 'anggotaLulus' => collect(), 'total' => $arsipTotal];

        $kelasList = Kelas::whereHas('siswas')->orderBy('nama')->get(['id', 'nama']);

        return view('ketua.anggota.index', compact('anggotas', 'peringatanCount', 'nonaktifCount', 'keluarCount', 'totalAnggotas', 'sort', 'direction', 'tampilArsip', 'arsip', 'arsipTotal', 'kelasList'));
    }

    public function calonSiswa(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        $ekskul = $this->ekskul();
        $statusAktif = [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN];

        $siswas = Siswa::query()
            ->with('kelas')
            ->whereDoesntHave('pendaftarans', fn ($query) => $query
                ->where('ekskul_id', $ekskul->id)
                ->whereIn('status', [Pendaftaran::STATUS_PENDING, Pendaftaran::STATUS_LULUS]))
            ->when($validated['kelas_id'] ?? null, fn ($query, $kelasId) => $query->where('kelas_id', $kelasId))
            ->when($validated['q'] ?? null, function ($query, $cari) {
                $query->where(function ($search) use ($cari) {
                    $search->where('nama', 'like', "%{$cari}%")
                        ->orWhere('nis', 'like', "%{$cari}%")
                        ->orWhereHas('kelas', fn ($kelas) => $kelas->where('nama', 'like', "%{$cari}%"));
                });
            })
            ->orderByRaw(
                'CASE WHEN EXISTS (
                    SELECT 1 FROM pendaftarans
                    WHERE pendaftarans.siswa_id = siswas.id
                    AND pendaftarans.ekskul_id = ?
                    AND pendaftarans.status IN (?, ?)
                ) THEN 1 ELSE 0 END',
                [$ekskul->id, ...$statusAktif]
            )
            ->orderBy('nama')
            ->paginate(25)
            ->withQueryString();

        $ids = $siswas->getCollection()->pluck('id');
        $statusSebelumnya = Pendaftaran::query()
            ->where('ekskul_id', $ekskul->id)
            ->whereIn('siswa_id', $ids)
            ->pluck('status', 'siswa_id');
        $tanggalSebelumnya = Pendaftaran::query()
            ->where('ekskul_id', $ekskul->id)
            ->whereIn('siswa_id', $ids)
            ->pluck('tanggal_daftar', 'siswa_id');

        return response()->json([
            'items' => $siswas->getCollection()->map(fn (Siswa $siswa) => [
                'id' => $siswa->id,
                'nama' => $siswa->nama,
                'nis' => $siswa->nis,
                'kelas' => $siswa->kelas?->nama ?? 'Tanpa kelas',
                'sudah_aktif' => in_array($statusSebelumnya->get($siswa->id), $statusAktif, true),
                'status_sebelumnya' => $statusSebelumnya->get($siswa->id),
                'tanggal_daftar_sebelumnya' => $tanggalSebelumnya->has($siswa->id)
                    ? \Illuminate\Support\Carbon::parse($tanggalSebelumnya->get($siswa->id))->format('d/m/Y')
                    : null,
            ])->values(),
            'total' => $siswas->total(),
            'current_page' => $siswas->currentPage(),
            'last_page' => $siswas->lastPage(),
        ]);
    }

    public function store(Request $request)
    {
        $ekskul = $this->ekskul();

        $validated = $request->validate([
            'siswa_ids' => ['required', 'array', 'min:1', 'max:100'],
            'siswa_ids.*' => ['required', 'integer', 'distinct', 'exists:siswas,id'],
        ], [
            'siswa_ids.required' => 'Pilih minimal satu siswa untuk didaftarkan.',
        ]);

        $count = DB::transaction(function () use ($validated, $ekskul) {
            $count = 0;

            foreach ($validated['siswa_ids'] as $siswaId) {
                $siswa = Siswa::whereKey($siswaId)->lockForUpdate()->firstOrFail();
                $pendaftaran = Pendaftaran::where('ekskul_id', $ekskul->id)
                    ->where('siswa_id', $siswa->id)
                    ->lockForUpdate()
                    ->first();

                if ($pendaftaran && ! in_array($pendaftaran->status, [
                    Pendaftaran::STATUS_NONAKTIF,
                    Pendaftaran::STATUS_KELUAR,
                    Pendaftaran::STATUS_DITOLAK,
                ], true)) {
                    throw ValidationException::withMessages([
                        'siswa_ids' => "{$siswa->nama} sudah aktif atau sedang menunggu di ekskul ini.",
                    ]);
                }

                if ($alasan = $siswa->alasanTidakDapatMengikutiEkskul($ekskul)) {
                    throw ValidationException::withMessages([
                        'siswa_ids' => "{$siswa->nama}: {$alasan}",
                    ]);
                }

                if ($pendaftaran) {
                    RiwayatPendaftaran::create([
                        'pendaftaran_id' => $pendaftaran->id,
                        'siswa_id' => $pendaftaran->siswa_id,
                        'ekskul_id' => $pendaftaran->ekskul_id,
                        'tanggal_daftar' => $pendaftaran->tanggal_daftar,
                        'status' => $pendaftaran->status,
                        'alasan' => $pendaftaran->alasan,
                        'dicatat_pada' => now(),
                    ]);

                    $pendaftaran->update([
                        'status' => Pendaftaran::STATUS_DITERIMA,
                        'tanggal_daftar' => now()->toDateString(),
                        'alasan' => 'Diaktifkan kembali oleh ketua.',
                    ]);
                } else {
                    $pendaftaran = Pendaftaran::create([
                        'siswa_id' => $siswa->id,
                        'ekskul_id' => $ekskul->id,
                        'status' => Pendaftaran::STATUS_DITERIMA,
                        'tanggal_daftar' => now()->toDateString(),
                        'alasan' => 'Didaftarkan langsung oleh ketua.',
                    ]);
                }

                if ($siswa->jabatan !== 'ketua') {
                    $siswa->update(['jabatan' => 'anggota']);
                }

                NotifikasiService::anggotaDitambahkan($pendaftaran);
                $count++;
            }

            return $count;
        });

        return back()->with('success', "Berhasil menambahkan {$count} siswa ke ekstrakurikuler.");
    }

    public function updateStatus(Request $request, Pendaftaran $pendaftaran)
    {
        $this->ensureEkskul($pendaftaran);

        $validated = $request->validate([
            'status' => ['required', 'in:diterima,peringatan,nonaktif'],
        ], [
            'status.required' => 'Status anggota wajib dipilih.',
            'status.in' => 'Status anggota tidak valid.',
        ]);

        $targetStatus = $validated['status'];

        $protected = $pendaftaran->siswa_id === auth()->user()->siswa?->id;

        if ($protected && in_array($targetStatus, [Pendaftaran::STATUS_PERINGATAN, Pendaftaran::STATUS_NONAKTIF])) {
            return back()->with('error', 'Kamu tidak bisa memberi peringatan atau menonaktifkan dirimu sendiri.');
        }

        if ($pendaftaran->status === $targetStatus) {
            return back()->with('error', 'Status anggota sudah '.$this->labelStatus($targetStatus).'.');
        }

        $pendaftaran->update(['status' => $targetStatus]);

        $namaSiswa = $pendaftaran->siswa?->nama ?? 'Siswa';

        [$judul, $pesan, $tipe] = match ($targetStatus) {
            Pendaftaran::STATUS_PERINGATAN => [
                'Peringatan dari Ketua Ekskul',
                $namaSiswa.', kamu mendapatkan peringatan dari ketua '.$pendaftaran->ekskul->nama_ekskul.'. Tingkatkan keaktifan dan kehadiranmu, ya!',
                'ditolak',
            ],
            Pendaftaran::STATUS_NONAKTIF => [
                'Dinonaktifkan dari Ekskul',
                $namaSiswa.', kamu telah dinonaktifkan dari ekskul '.$pendaftaran->ekskul->nama_ekskul.' oleh ketua ekskul. Hubungi ketua jika ini kurang tepat.',
                'ditolak',
            ],
            default => [
                'Status Anggota Diaktifkan Kembali',
                $namaSiswa.', status anggota kamu di ekskul '.$pendaftaran->ekskul->nama_ekskul.' telah diaktifkan kembali. Selamat beraktivitas!',
                'diterima',
            ],
        };

        NotifikasiService::statusAnggotaDiubah($pendaftaran, $targetStatus, $judul, $pesan, $tipe);

        return back()->with('success', 'Status '.$namaSiswa.' berhasil diubah menjadi '.$this->labelStatus($targetStatus).'.');
    }

    private function labelStatus(string $status): string
    {
        return match ($status) {
            Pendaftaran::STATUS_DITERIMA => 'Aktif',
            Pendaftaran::STATUS_PERINGATAN => 'Peringatan',
            Pendaftaran::STATUS_NONAKTIF => 'Nonaktif',
            Pendaftaran::STATUS_KELUAR => 'Keluar',
            Pendaftaran::STATUS_LULUS => 'Lulus',
            default => $status,
        };
    }
}
