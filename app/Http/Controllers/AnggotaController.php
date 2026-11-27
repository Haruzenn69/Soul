<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Pendaftaran;
use App\Models\Presensi;
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
        $user = auth()->user();
        $isKesiswaan = $user && in_array($user->role, ['kesiswaan', 'admin'], true);

        if ($isKesiswaan && ! $request->filled('ekskul')) {
            $ekskuls = \App\Models\Ekskul::whereNull('parent_id')->orderBy('nama_ekskul')->get();
            $cards = $ekskuls->map(function (\App\Models\Ekskul $e) {
                $ids = array_merge([$e->id], $e->subdivisis()->pluck('id')->all());
                $totalAktif = Pendaftaran::whereIn('ekskul_id', $ids)
                    ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
                    ->count();
                $peringatan = Pendaftaran::whereIn('ekskul_id', $ids)
                    ->where('status', Pendaftaran::STATUS_PERINGATAN)
                    ->count();
                $ketua = $e->ketua();

                $badge = null;
                if ($peringatan > 0) {
                    $badge = ['text' => $peringatan.' Peringatan', 'class' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300'];
                }

                $stats = [
                    ['label' => 'Anggota Aktif', 'value' => $totalAktif.' Siswa', 'class' => 'font-bold text-slate-800 dark:text-slate-200'],
                    ['label' => 'Ketua Ekskul', 'value' => $ketua?->nama ?? 'Belum ada', 'class' => 'font-semibold text-slate-600 dark:text-slate-300 truncate max-w-[120px]'],
                ];

                return (object) [
                    'ekskul' => $e,
                    'stats' => $stats,
                    'badge' => $badge,
                    'actionText' => 'Kelola Anggota',
                    'actionUrl' => route('ketua.anggota.index', ['ekskul' => $e->id]),
                ];
            });

            return view('ketua.anggota.index', [
                'isCatalog' => true,
                'cards' => $cards,
                'isKesiswaan' => true,
            ]);
        }

        $ekskul = $this->ekskul();
        $anggotaEkskulIds = $this->anggotaEkskulIds($ekskul);
        $subdivisis = ! $ekskul->isSubdivisi() ? $ekskul->subdivisis()->orderBy('nama_ekskul')->get() : collect();
        $base = Pendaftaran::whereIn('ekskul_id', $anggotaEkskulIds)
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
            ->when($request->filled('status') && $statusDipilih !== 'semua', fn ($query) => $query->where('status', $statusDipilih))
            ->when($request->filled('kelas_id'), fn ($query) => $query->whereHas('siswa', fn ($s) => $s->where('kelas_id', $request->input('kelas_id'))));

        $daftarKelas = \App\Models\Kelas::query()
            ->whereHas('siswas.pendaftarans', fn ($p) => $p
                ->whereIn('ekskul_id', $anggotaEkskulIds)
                ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_NONAKTIF, Pendaftaran::STATUS_PERINGATAN, Pendaftaran::STATUS_KELUAR]))
            ->orderBy('nama')
            ->get();

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
        $ketuaId = $ekskul->ketua()?->id;
        $anggotaPusatLama = ! $ekskul->isSubdivisi() && $subdivisis->isNotEmpty()
            ? Pendaftaran::query()->where('ekskul_id', $ekskul->id)
                ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN, Pendaftaran::STATUS_NONAKTIF])
                ->with('siswa.kelas')->orderBy('tanggal_daftar')->get()
            : collect();
        $ketuaSubdivisiAssignments = ! $ekskul->isSubdivisi() && $subdivisis->isNotEmpty()
            ? \App\Models\Pengurus::query()->where('ekskul_id', $ekskul->id)
                ->where('jabatan', \App\Models\Pengurus::JABATAN_WAKIL_KETUA)
                ->where('status', \App\Models\Pengurus::STATUS_AKTIF)
                ->whereIn('subdivisi_id', $subdivisis->pluck('id'))
                ->pluck('subdivisi_id', 'siswa_id')
            : collect();

        return view('ketua.anggota.index', compact('anggotas', 'ekskul', 'subdivisis', 'anggotaPusatLama', 'ketuaSubdivisiAssignments', 'peringatanCount', 'nonaktifCount', 'keluarCount', 'totalAnggotas', 'daftarKelas', 'sort', 'direction', 'tampilArsip', 'arsip', 'arsipTotal', 'isKesiswaan', 'ketuaId'));
    }

    public function calonSiswa(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        $ekskul = $this->ekskul();
        $targetEkskul = $this->targetAnggotaEkskul($request);
        $scopeEkskulIds = $this->anggotaEkskulIds($ekskul);
        if ($targetEkskul->isSubdivisi()) {
            $scopeEkskulIds[] = $targetEkskul->parent_id;
        }
        $statusAktif = [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN];

        $siswas = Siswa::query()
            ->with('kelas')
            ->whereDoesntHave('pendaftarans', fn ($query) => $query
                ->whereIn('ekskul_id', $scopeEkskulIds)
                ->whereIn('status', [Pendaftaran::STATUS_PENDING, Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN, Pendaftaran::STATUS_LULUS]))
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
                [$targetEkskul->id, ...$statusAktif]
            )
            ->orderBy('nama')
            ->paginate(25)
            ->withQueryString();

        $ids = $siswas->getCollection()->pluck('id');
        $statusSebelumnya = Pendaftaran::query()
            ->whereIn('ekskul_id', $scopeEkskulIds)
            ->whereIn('siswa_id', $ids)
            ->pluck('status', 'siswa_id');
        $tanggalSebelumnya = Pendaftaran::query()
            ->whereIn('ekskul_id', $scopeEkskulIds)
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

    public function show(Request $request, Pendaftaran $pendaftaran)
    {
        $this->ensureEkskul($pendaftaran);

        $pendaftaran->load([
            'siswa.kelas',
            'ekskul.pembina.user',
            'ekskul.pelatih',
            'siswa.pendaftarans.ekskul',
            'siswa.pengajuanKeluars.ekskul',
            'siswa.riwayatJabatans.ekskul',
        ]);

        $presensiStats = Presensi::where('pendaftaran_id', $pendaftaran->id)
            ->selectRaw('
                count(*) as total,
                sum(case when status = ? then 1 else 0 end) as hadir
            ', [Presensi::STATUS_HADIR])
            ->first();

        $totalPresensi = (int) ($presensiStats->total ?? 0);
        $hadir = (int) ($presensiStats->hadir ?? 0);
        $persentase = $totalPresensi > 0 ? round(($hadir / $totalPresensi) * 100, 1) : 0;
        $backUrl = route('ketua.anggota.index', array_merge(
            $request->query(),
            ['ekskul' => $pendaftaran->ekskul_id],
        ));

        return view('pembina.anggota.show', [
            'pendaftaran' => $pendaftaran,
            'ekskuls' => collect([$pendaftaran->ekskul]),
            'totalPresensi' => $totalPresensi,
            'hadir' => $hadir,
            'persentase' => $persentase,
            'isKetuaView' => true,
            'backUrl' => $backUrl,
        ]);
    }

    public function store(Request $request)
    {
        $ekskul = $this->ekskul();
        $ekskul = $this->targetAnggotaEkskul($request);

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

        return redirect()->route('ketua.pendaftaran.index')->with('success', "Berhasil menambahkan {$count} siswa ke ekstrakurikuler.");
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

    public function tempatkanAnggotaKeSubdivisi(Request $request)
    {
        $ekskul = $this->ekskul();
        abort_unless(! $ekskul->isSubdivisi() && $ekskul->hasSubdivisi(), 422, 'Penempatan hanya tersedia untuk ekskul pusat yang memiliki subdivisi.');

        $user = auth()->user();
        $siswa = $user?->siswa;
        $isKesiswaan = $user && in_array($user->role, ['kesiswaan', 'admin'], true);
        abort_unless($isKesiswaan || $siswa?->ekskulDipimpin()?->id === $ekskul->id, 403, 'Hanya Ketua pusat yang dapat menyelesaikan penempatan anggota ini.');

        $subdivisis = $ekskul->subdivisis()->orderBy('id')->get();
        $ketuaAssignments = \App\Models\Pengurus::query()
            ->where('ekskul_id', $ekskul->id)
            ->where('jabatan', \App\Models\Pengurus::JABATAN_WAKIL_KETUA)
            ->where('status', \App\Models\Pengurus::STATUS_AKTIF)
            ->whereIn('subdivisi_id', $subdivisis->pluck('id'))
            ->pluck('subdivisi_id', 'siswa_id');
        $eligibleStatuses = [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN, Pendaftaran::STATUS_NONAKTIF];
        $members = Pendaftaran::query()->where('ekskul_id', $ekskul->id)->whereIn('status', $eligibleStatuses)->get();
        abort_if($members->isEmpty(), 422, 'Tidak ada anggota ekskul pusat yang perlu ditempatkan.');

        $validated = $request->validate([
            'penempatan' => ['required', 'array'],
            'penempatan.*' => ['required', 'integer'],
        ], ['penempatan.required' => 'Pilih subdivisi untuk setiap anggota.']);

        $divisionIds = $subdivisis->pluck('id')->map(fn ($id) => (int) $id)->all();
        $placements = [];
        foreach ($members as $member) {
            $leaderDivision = $ketuaAssignments->get($member->siswa_id);
            $targetId = $leaderDivision ?? ($validated['penempatan'][$member->id] ?? null);
            abort_unless($targetId && in_array((int) $targetId, $divisionIds, true), 422, 'Setiap anggota harus dipilih tepat satu kali ke subdivisi yang tersedia.');
            $placements[$member->id] = (int) $targetId;
        }

        DB::transaction(function () use ($ekskul, $placements) {
            foreach ($placements as $pendaftaranId => $targetId) {
                $pendaftaran = Pendaftaran::query()->where('ekskul_id', $ekskul->id)->lockForUpdate()->findOrFail($pendaftaranId);
                $subdivisi = $ekskul->subdivisis()->findOrFail($targetId);
                $duplicate = Pendaftaran::query()->where('siswa_id', $pendaftaran->siswa_id)->where('ekskul_id', $subdivisi->id)->lockForUpdate()->first();

                if ($duplicate) {
                    if (! in_array($duplicate->status, [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN], true)) {
                        RiwayatPendaftaran::create([
                            'pendaftaran_id' => $duplicate->id,
                            'siswa_id' => $duplicate->siswa_id,
                            'ekskul_id' => $duplicate->ekskul_id,
                            'tanggal_daftar' => $duplicate->tanggal_daftar,
                            'status' => $duplicate->status,
                            'alasan' => 'Diaktifkan saat penempatan anggota ke subdivisi.',
                            'dicatat_pada' => now(),
                        ]);
                        $duplicate->update(['status' => $pendaftaran->status === Pendaftaran::STATUS_NONAKTIF ? Pendaftaran::STATUS_NONAKTIF : Pendaftaran::STATUS_DITERIMA]);
                    }

                    RiwayatPendaftaran::create([
                        'pendaftaran_id' => $pendaftaran->id,
                        'siswa_id' => $pendaftaran->siswa_id,
                        'ekskul_id' => $pendaftaran->ekskul_id,
                        'tanggal_daftar' => $pendaftaran->tanggal_daftar,
                        'status' => $pendaftaran->status,
                        'alasan' => 'Keanggotaan pusat disatukan dengan riwayat subdivisi '.$subdivisi->nama_ekskul.'.',
                        'dicatat_pada' => now(),
                    ]);
                    $pendaftaran->update(['status' => Pendaftaran::STATUS_KELUAR, 'alasan' => 'Keanggotaan dipusatkan pada subdivisi '.$subdivisi->nama_ekskul.'.']);
                    continue;
                }

                RiwayatPendaftaran::create([
                    'pendaftaran_id' => $pendaftaran->id,
                    'siswa_id' => $pendaftaran->siswa_id,
                    'ekskul_id' => $pendaftaran->ekskul_id,
                    'tanggal_daftar' => $pendaftaran->tanggal_daftar,
                    'status' => $pendaftaran->status,
                    'alasan' => 'Penempatan anggota dari ekskul pusat ke subdivisi '.$subdivisi->nama_ekskul.'.',
                    'dicatat_pada' => now(),
                ]);

                $pendaftaran->update(['ekskul_id' => $subdivisi->id]);
            }
        });

        return redirect()->route('ketua.anggota.index', ['ekskul' => $ekskul->id])->with('success', 'Penempatan '.$members->count().' anggota ke subdivisi berhasil disimpan. Proses penempatan pusat sudah selesai.');
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
