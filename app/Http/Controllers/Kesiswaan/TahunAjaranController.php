<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Pendaftaran;
use App\Models\Siswa;
use App\Models\SiswaRiwayatKelas;
use App\Models\TahunAjaran;
use App\Services\AcademicYearRolloverService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TahunAjaranController extends Controller
{
    /**
     * Main page: list all tahun ajaran with their classes.
     */
    public function index(): View
    {
        $tahunAjarans = TahunAjaran::query()
            ->withCount('kelas')
            ->orderByRaw("FIELD(status, 'aktif', 'nonaktif', 'historis')")
            ->orderByDesc('nama')
            ->get();

        // Load active TA details
        $activeTA = $tahunAjarans->firstWhere('status', 'aktif');
        $activeKelas = $activeTA
            ? Kelas::where('tahun_ajaran_id', $activeTA->id)
                ->withCount('siswas')
                ->orderByRaw("FIELD(tingkat, 'x', 'xi', 'xii')")
                ->orderBy('jurusan')
                ->orderBy('rombel')
                ->get()
            : collect();

        // Count active students in Kelas 10 (can be promoted to Kelas 11)
        $siswaKelas10 = $activeTA
            ? Siswa::whereHas('kelas', fn ($q) => $q->where('tahun_ajaran_id', $activeTA->id)->where('tingkat', 'x'))
                ->where('status', 'aktif')
                ->count()
            : 0;

        $pendingSiswa = Siswa::where('status', 'menunggu_penempatan')->count();

        $kelasByTA = [];
        foreach ($tahunAjarans as $ta) {
            if ($ta->status === 'aktif') {
                $kelasByTA[$ta->id] = $activeKelas;
            } else {
                $kelasItems = Kelas::where('tahun_ajaran_id', $ta->id)
                    ->orderByRaw("FIELD(tingkat, 'x', 'xi', 'xii')")
                    ->orderBy('jurusan')
                    ->orderBy('rombel')
                    ->get();

                if ($ta->status === 'historis') {
                    foreach ($kelasItems as $item) {
                        $item->siswas_count = Siswa::query()->where(function ($query) use ($item, $ta) {
                            $query->whereHas('classHistories', fn ($history) => $history
                                    ->where('tahun_ajaran', $ta->nama)
                                    ->where('kelas_asal', $item->nama))
                                ->orWhere(function ($sub) use ($item, $ta) {
                                    $sub->where('kelas_id', $item->id)
                                        ->whereDoesntHave('classHistories', fn ($h) => $h->where('tahun_ajaran', $ta->nama));
                                });
                        })->distinct()->count('siswas.id');
                    }
                } else {
                    foreach ($kelasItems as $item) {
                        $item->siswas_count = Siswa::where('kelas_id', $item->id)->count();
                    }
                }
                $kelasByTA[$ta->id] = $kelasItems;
            }
        }

        return view('kesiswaan.tahun-ajaran.index', compact('tahunAjarans', 'activeTA', 'activeKelas', 'pendingSiswa', 'siswaKelas10', 'kelasByTA'));
    }

    /**
     * Create a new tahun ajaran (status: nonaktif).
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:20', 'unique:tahun_ajarans,nama', 'regex:/^\d{4}\/\d{4}$/'],
        ], [
            'nama.regex' => 'Format nama tahun ajaran harus YYYY/YYYY (contoh: 2027/2028).',
            'nama.unique' => 'Tahun ajaran ini sudah ada.',
        ]);

        TahunAjaran::create([
            'nama' => $data['nama'],
            'status' => 'nonaktif',
        ]);

        return back()->with('success', "Tahun ajaran {$data['nama']} berhasil dibuat.");
    }

    /**
     * Delete an unused (nonaktif) tahun ajaran.
     */
    public function destroy(TahunAjaran $tahunAjaran): RedirectResponse
    {
        if ($tahunAjaran->status !== 'nonaktif') {
            return back()->with('error', 'Hanya tahun ajaran nonaktif yang bisa dihapus.');
        }

        // Check if it has any kelas with students
        $hasStudents = Kelas::where('tahun_ajaran_id', $tahunAjaran->id)
            ->whereHas('siswas')
            ->exists();

        if ($hasStudents) {
            return back()->with('error', 'Tahun ajaran ini masih memiliki kelas dengan siswa.');
        }

        $nama = $tahunAjaran->nama;
        // Delete associated kelas first
        Kelas::where('tahun_ajaran_id', $tahunAjaran->id)->delete();
        $tahunAjaran->delete();

        return back()->with('success', "Tahun ajaran {$nama} berhasil dihapus.");
    }

    /**
     * Activate a tahun ajaran → triggers class promotion.
     */
    public function activate(Request $request, TahunAjaran $tahunAjaran): RedirectResponse
    {
        if ($tahunAjaran->status !== 'nonaktif') {
            return back()->with('error', 'Hanya tahun ajaran nonaktif yang bisa diaktifkan.');
        }

        try {
            $service = new AcademicYearRolloverService();
            $result = $service->activateTahunAjaran($tahunAjaran);

            if ($result['already_done']) {
                return back()->with('error', 'Tahun ajaran ini sudah aktif.');
            }

            $msg = "Tahun ajaran {$result['to']} berhasil diaktifkan.";
            if ($result['from']) {
                $msg .= " {$result['promoted']} siswa naik ke kelas 12, {$result['graduated']} siswa lulus, dan {$result['pending']} siswa kelas 10 tetap di kelas 10 tahun ajaran baru (siap dinaikkan ke kelas 11).";
            }

            return back()->with('success', $msg);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Deactivate an active tahun ajaran (becomes historis).
     * Snapshots ALL students in ALL classes of this tahun ajaran.
     */
    public function deactivate(TahunAjaran $tahunAjaran): RedirectResponse
    {
        if ($tahunAjaran->status !== 'aktif') {
            return back()->with('error', 'Hanya tahun ajaran aktif yang bisa dinonaktifkan.');
        }

        DB::transaction(function () use ($tahunAjaran) {
            $now = now('Asia/Jakarta');
            $classes = Kelas::where('tahun_ajaran_id', $tahunAjaran->id)
                ->with(['siswas' => fn ($q) => $q->where('status', 'aktif')])
                ->get();

            foreach ($classes as $class) {
                foreach ($class->siswas as $student) {
                    $alreadyRecorded = SiswaRiwayatKelas::where('siswa_id', $student->id)
                        ->where('tahun_ajaran', $tahunAjaran->nama)
                        ->exists();

                    if (! $alreadyRecorded) {
                        SiswaRiwayatKelas::create([
                            'siswa_id' => $student->id,
                            'tahun_ajaran' => $tahunAjaran->nama,
                            'kelas_asal' => $class->nama,
                            'kelas_tujuan' => $class->nama,
                            'jenis' => 'penempatan',
                            'diproses_pada' => $now,
                        ]);
                    }
                }
            }

            $tahunAjaran->update(['status' => 'historis']);
        });

        return back()->with('success', "Tahun ajaran {$tahunAjaran->nama} berhasil dinonaktifkan dan dijadikan arsip historis.");
    }

    /**
     * Historical view of all students in a historical tahun ajaran.
     */
    public function historis(Request $request, TahunAjaran $tahunAjaran): View
    {
        abort_unless($tahunAjaran->isHistoris(), 404);

        $sort = in_array($request->input('sort'), ['nama', 'kelas'], true) ? $request->input('sort') : 'nama';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';
        $kelasFilter = $request->input('kelas_id');

        $kelasIds = $tahunAjaran->kelas->pluck('id');
        $kelasNames = $tahunAjaran->kelas->pluck('nama');

        $siswaQuery = Siswa::query()
            ->with(['classHistories', 'kelas'])
            ->where(function ($query) use ($kelasIds, $tahunAjaran, $kelasNames) {
                $query->whereIn('kelas_id', $kelasIds)
                    ->orWhereHas('classHistories', fn ($h) => $h
                        ->where('tahun_ajaran', $tahunAjaran->nama)
                        ->whereIn('kelas_asal', $kelasNames));
            });

        if ($kelasFilter) {
            $selectedKelas = Kelas::find($kelasFilter);
            if ($selectedKelas) {
                $siswaQuery->where(function ($q) use ($selectedKelas, $tahunAjaran) {
                    $q->whereHas('classHistories', fn ($h) => $h
                        ->where('tahun_ajaran', $tahunAjaran->nama)
                        ->where('kelas_asal', $selectedKelas->nama))
                    ->orWhere(function ($sub) use ($selectedKelas, $tahunAjaran) {
                        $sub->where('kelas_id', $selectedKelas->id)
                            ->whereDoesntHave('classHistories', fn ($h) => $h->where('tahun_ajaran', $tahunAjaran->nama));
                    });
                });
            }
        }

        $siswas = (clone $siswaQuery)
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%'.$request->input('q').'%';
                $query->where(fn ($sub) => $sub->where('nama', 'like', $q)->orWhere('nis', 'like', $q));
            })
            ->orderBy('nama', $direction)
            ->paginate(25)
            ->withQueryString();

        $siswaIds = $siswas->pluck('id');
        $ekskulData = Pendaftaran::whereIn('siswa_id', $siswaIds)
            ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN, Pendaftaran::STATUS_LULUS])
            ->with('ekskul')
            ->get()
            ->groupBy('siswa_id');

        $kelasList = $tahunAjaran->kelas()
            ->orderByRaw("FIELD(tingkat, 'x', 'xi', 'xii')")
            ->orderBy('jurusan')
            ->orderBy('rombel')
            ->get();

        return view('kesiswaan.tahun-ajaran.historis', compact('tahunAjaran', 'siswas', 'ekskulData', 'kelasList', 'sort', 'direction'));
    }

    /**
     * Show detail of a kelas (students list) for active tahun ajaran.
     */
    public function showKelas(Request $request, TahunAjaran $tahunAjaran, Kelas $kela): View
    {
        abort_unless((int) $kela->tahun_ajaran_id === (int) $tahunAjaran->id, 404);

        $sort = in_array($request->input('sort'), ['nama', 'nis', 'created_at'], true) ? $request->input('sort') : 'nama';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        if ($tahunAjaran->isHistoris()) {
            // Historical view: show students that WERE in this class
            $siswaQuery = Siswa::query()
                ->with(['classHistories', 'kelas'])
                ->where(function ($query) use ($kela, $tahunAjaran) {
                    $query->whereHas('classHistories', fn ($h) => $h
                        ->where('tahun_ajaran', $tahunAjaran->nama)
                        ->where('kelas_asal', $kela->nama))
                    ->orWhere(function ($sub) use ($kela, $tahunAjaran) {
                        $sub->where('kelas_id', $kela->id)
                            ->whereDoesntHave('classHistories', fn ($h) => $h->where('tahun_ajaran', $tahunAjaran->nama));
                    });
                });

            $siswas = (clone $siswaQuery)
                ->when($request->filled('q'), function ($query) use ($request) {
                    $q = '%'.$request->input('q').'%';
                    $query->where(fn ($sub) => $sub->where('nama', 'like', $q)->orWhere('nis', 'like', $q));
                })
                ->orderBy($sort, $direction)
                ->paginate(20)
                ->withQueryString();

            // Load ekskul data for historical view (including lulus)
            $siswaIds = $siswas->pluck('id');
            $ekskulData = Pendaftaran::whereIn('siswa_id', $siswaIds)
                ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN, Pendaftaran::STATUS_LULUS])
                ->with('ekskul')
                ->get()
                ->groupBy('siswa_id');

            return view('kesiswaan.tahun-ajaran.show-historis', compact('tahunAjaran', 'kela', 'siswas', 'ekskulData', 'sort', 'direction'));
        }

        // Active view
        $siswas = Siswa::where('kelas_id', $kela->id)
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%'.$request->input('q').'%';
                $query->where(fn ($sub) => $sub->where('nama', 'like', $q)->orWhere('nis', 'like', $q));
            })
            ->orderBy($sort, $direction)
            ->paginate(20)
            ->withQueryString();

        // For kelas 11: allow taking students from an active kelas 10
        $sourceKelasOptions = collect();
        $selectedSourceKelas = null;
        $availableSiswa = collect();

        if ($tahunAjaran->isAktif() && $kela->tingkat === 'xi') {
            $sourceKelasOptions = Kelas::where('tahun_ajaran_id', $tahunAjaran->id)
                ->where('tingkat', 'x')
                ->withCount('siswas')
                ->orderByRaw("FIELD(jurusan, '{$kela->jurusan}') DESC")
                ->orderBy('jurusan')
                ->orderBy('rombel')
                ->get();

            $selectedSourceId = $request->input('source_kelas_id') ?: ($sourceKelasOptions->firstWhere('jurusan', $kela->jurusan)?->id ?? $sourceKelasOptions->first()?->id);
            $selectedSourceKelas = $sourceKelasOptions->firstWhere('id', (int) $selectedSourceId);

            if ($selectedSourceKelas) {
                $availableSiswa = Siswa::where('kelas_id', $selectedSourceKelas->id)
                    ->where('status', 'aktif')
                    ->orderBy('nama')
                    ->get();
            }
        }

        // For kelas 10: allow promoting students to a kelas 11
        $targetKelasOptions = collect();
        if ($tahunAjaran->isAktif() && $kela->tingkat === 'x') {
            $targetKelasOptions = Kelas::where('tahun_ajaran_id', $tahunAjaran->id)
                ->where('tingkat', 'xi')
                ->withCount('siswas')
                ->orderByRaw("FIELD(jurusan, '{$kela->jurusan}') DESC")
                ->orderBy('jurusan')
                ->orderBy('rombel')
                ->get();
        }

        $stats = [
            'total' => Siswa::where('kelas_id', $kela->id)->count(),
            'laki' => Siswa::where('kelas_id', $kela->id)->where('jenis_kelamin', 'laki-laki')->count(),
            'perempuan' => Siswa::where('kelas_id', $kela->id)->where('jenis_kelamin', 'perempuan')->count(),
        ];

        return view('kesiswaan.tahun-ajaran.show', compact(
            'tahunAjaran',
            'kela',
            'siswas',
            'stats',
            'availableSiswa',
            'sourceKelasOptions',
            'selectedSourceKelas',
            'targetKelasOptions',
            'sort',
            'direction'
        ));
    }

    /**
     * Promote / assign students from a kelas 10 to a kelas 11.
     */
    public function assignSiswa(Request $request, TahunAjaran $tahunAjaran, Kelas $kela): RedirectResponse
    {
        abort_unless($tahunAjaran->isAktif(), 403, 'Tahun ajaran tidak aktif.');
        abort_unless((int) $kela->tahun_ajaran_id === (int) $tahunAjaran->id, 404);

        $data = $request->validate([
            'siswa_ids' => ['required', 'array', 'min:1'],
            'siswa_ids.*' => ['required', 'integer', 'distinct', 'exists:siswas,id'],
            'target_kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
        ]);

        $targetKelas = $kela->tingkat === 'xi'
            ? $kela
            : Kelas::where('id', $data['target_kelas_id'] ?? null)->where('tahun_ajaran_id', $tahunAjaran->id)->where('tingkat', 'xi')->firstOrFail();

        $result = DB::transaction(function () use ($data, $targetKelas, $tahunAjaran) {
            $siswas = Siswa::with('kelas.tahunAjaran')->whereIn('id', $data['siswa_ids'])->lockForUpdate()->get();

            if ($siswas->count() !== count($data['siswa_ids'])) {
                throw ValidationException::withMessages(['siswa_ids' => 'Sebagian siswa tidak ditemukan.']);
            }

            $now = now('Asia/Jakarta');
            foreach ($siswas as $siswa) {
                SiswaRiwayatKelas::create([
                    'siswa_id' => $siswa->id,
                    'tahun_ajaran' => $tahunAjaran->nama,
                    'kelas_asal' => $siswa->kelas?->nama ?? '-',
                    'kelas_tujuan' => $targetKelas->nama,
                    'jenis' => 'kenaikan',
                    'diproses_pada' => $now,
                ]);

                $siswa->update(['kelas_id' => $targetKelas->id, 'status' => 'aktif']);
            }

            return $siswas->count();
        }, 3);

        return back()->with('success', "{$result} siswa berhasil dinaikkan ke kelas {$targetKelas->nama}.");
    }


}
