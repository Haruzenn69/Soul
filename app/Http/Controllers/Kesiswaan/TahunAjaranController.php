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
                ->withCount(['siswas as siswa_menunggu_count' => fn ($query) => $query->where('status', 'menunggu_penempatan')])
                ->orderByRaw("FIELD(tingkat, 'x', 'xi', 'xii')")
                ->orderBy('jurusan')
                ->orderBy('rombel')
                ->get()
            : collect();

        // Count active students in Kelas 10 (can be promoted to Kelas 11)
        $siswaKelas10 = $activeTA
            ? Siswa::whereHas('kelas', fn ($q) => $q->where('tahun_ajaran_id', $activeTA->id)->where('tingkat', 'x'))
                ->whereIn('status', ['aktif', 'menunggu_penempatan'])
                ->count()
            : 0;

        $pendingSiswa = Siswa::where('status', 'menunggu_penempatan')->count();

        // Source year (prior academic year)
        $sourceYear = TahunAjaran::query()->where('status', 'historis')->orderByDesc('id')->first();

        // Kelas 12 from previous year waiting for graduation decision
        $pendingKelas12Classes = $sourceYear
            ? Kelas::where('tahun_ajaran_id', $sourceYear->id)
                ->where('tingkat', 'xii')
                ->with(['siswas' => fn ($q) => $q->where('status', 'menunggu_penempatan')->orderBy('nama')])
                ->get()
                ->filter(fn ($k) => $k->siswas->isNotEmpty())
                ->values()
            : collect();
        $pendingCount12 = $pendingKelas12Classes->sum(fn ($k) => $k->siswas->count());

        // Students waiting for promotion from 11 to 12
        $pendingCount11 = Siswa::where('status', 'menunggu_penempatan')
            ->whereHas('kelas', fn ($q) => $q->where('tingkat', 'xi'))
            ->count();

        // Students waiting for placement from 10 to 11
        $pendingCount10 = Siswa::where('status', 'menunggu_penempatan')
            ->whereHas('kelas', fn ($q) => $q->where('tingkat', 'x'))
            ->count();

        // New students (PPDB / unassigned)
        $pendingCountBaru = Siswa::where('status', 'menunggu_penempatan')
            ->where(function ($q) {
                $q->whereNull('kelas_id')->orWhereDoesntHave('kelas');
            })
            ->count();

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

        return view('kesiswaan.tahun-ajaran.index', compact(
            'tahunAjarans',
            'activeTA',
            'activeKelas',
            'sourceYear',
            'pendingSiswa',
            'siswaKelas10',
            'pendingKelas12Classes',
            'pendingCount12',
            'pendingCount11',
            'pendingCount10',
            'pendingCountBaru',
            'kelasByTA'
        ));
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
                $msg .= ' Klik kartu kelas pada tahun ajaran baru untuk memproses siswa yang menunggu penempatan.';
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
                ->with(['siswas' => fn ($q) => $q->whereIn('status', ['aktif', 'menunggu_penempatan'])])
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

        foreach ($kelasList as $item) {
            $item->siswas_count = Siswa::query()->where(function ($query) use ($item, $tahunAjaran) {
                $query->whereHas('classHistories', fn ($history) => $history
                        ->where('tahun_ajaran', $tahunAjaran->nama)
                        ->where('kelas_asal', $item->nama))
                    ->orWhere(function ($sub) use ($item, $tahunAjaran) {
                        $sub->where('kelas_id', $item->id)
                            ->whereDoesntHave('classHistories', fn ($h) => $h->where('tahun_ajaran', $tahunAjaran->nama));
                    });
            })->distinct()->count('siswas.id');
        }

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
            ->where('status', 'aktif')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%'.$request->input('q').'%';
                $query->where(fn ($sub) => $sub->where('nama', 'like', $q)->orWhere('nis', 'like', $q));
            })
            ->orderBy($sort, $direction)
            ->paginate(20)
            ->withQueryString();

        $sourceYear = TahunAjaran::query()->where('status', 'historis')->orderByDesc('id')->first();
        $sourceKelasOptions = collect();
        $availableSiswa = collect();
        $matchingSourceClass = null;

        if ($tahunAjaran->isAktif()) {
            if ($kela->tingkat === 'xi') {
                // Kelas 10 -> 11: Pindah kelas dibatasi per jurusan
                $allowedJurusans = [$kela->jurusan];
                if ($kela->jurusan === 'mp') $allowedJurusans[] = 'mlog';
                if ($kela->jurusan === 'mlog') $allowedJurusans[] = 'mp';

                $sourceKelasOptions = Kelas::where(function ($q) use ($sourceYear, $tahunAjaran) {
                        if ($sourceYear) {
                            $q->where('tahun_ajaran_id', $sourceYear->id);
                        } else {
                            $q->where('tahun_ajaran_id', $tahunAjaran->id);
                        }
                    })
                    ->where('tingkat', 'x')
                    ->whereIn('jurusan', $allowedJurusans)
                    ->withCount(['siswas as waiting_siswas_count' => fn ($q) => $q->where('status', 'menunggu_penempatan')])
                    ->orderBy('jurusan')
                    ->orderBy('rombel')
                    ->get();

                if ($sourceKelasOptions->isEmpty()) {
                    $sourceKelasOptions = Kelas::where('tingkat', 'x')
                        ->whereIn('jurusan', $allowedJurusans)
                        ->whereHas('siswas', fn ($q) => $q->where('status', 'menunggu_penempatan'))
                        ->withCount(['siswas as waiting_siswas_count' => fn ($q) => $q->where('status', 'menunggu_penempatan')])
                        ->get();
                }

                $selectedSourceId = $request->input('source_kelas_id');
                $availableSiswaQuery = Siswa::with('kelas')
                    ->where('status', 'menunggu_penempatan')
                    ->whereHas('kelas', function ($q) use ($allowedJurusans) {
                        $q->where('tingkat', 'x')->whereIn('jurusan', $allowedJurusans);
                    });

                if ($selectedSourceId) {
                    $availableSiswaQuery->where('kelas_id', $selectedSourceId);
                }
                $availableSiswa = $availableSiswaQuery->orderBy('nama')->get();

            } elseif ($kela->tingkat === 'xii') {
                // Kelas 11 -> 12: Rombel tetap (11 RPL 1 -> 12 RPL 1)
                $matchingSourceClass = Kelas::where(function ($q) use ($sourceYear, $tahunAjaran) {
                        if ($sourceYear) {
                            $q->where('tahun_ajaran_id', $sourceYear->id);
                        } else {
                            $q->where('tahun_ajaran_id', $tahunAjaran->id);
                        }
                    })
                    ->where('tingkat', 'xi')
                    ->where('jurusan', $kela->jurusan)
                    ->where('rombel', $kela->rombel)
                    ->first();

                if (! $matchingSourceClass) {
                    $matchingSourceClass = Kelas::where('tingkat', 'xi')
                        ->where('jurusan', $kela->jurusan)
                        ->where('rombel', $kela->rombel)
                        ->first();
                }

                if ($matchingSourceClass) {
                    $availableSiswa = Siswa::with('kelas')
                        ->where('kelas_id', $matchingSourceClass->id)
                        ->where('status', 'menunggu_penempatan')
                        ->orderBy('nama')
                        ->get();
                }

            } elseif ($kela->tingkat === 'x') {
                // Kelas 10 Baru: Siswa baru / belum punya kelas
                $availableSiswa = Siswa::with('kelas')
                    ->where('status', 'menunggu_penempatan')
                    ->where(function ($query) {
                        $query->whereNull('kelas_id')
                            ->orWhereDoesntHave('kelas')
                            ->orWhereHas('kelas', fn ($q) => $q->where('tingkat', 'x'));
                    })
                    ->orderBy('nama')
                    ->get();
            }
        }

        $stats = [
            'total' => Siswa::where('kelas_id', $kela->id)->where('status', 'aktif')->count(),
            'laki' => Siswa::where('kelas_id', $kela->id)->where('status', 'aktif')->where('jenis_kelamin', 'laki-laki')->count(),
            'perempuan' => Siswa::where('kelas_id', $kela->id)->where('status', 'aktif')->where('jenis_kelamin', 'perempuan')->count(),
        ];

        return view('kesiswaan.tahun-ajaran.show', compact(
            'tahunAjaran',
            'kela',
            'siswas',
            'stats',
            'availableSiswa',
            'sourceKelasOptions',
            'matchingSourceClass',
            'sourceYear',
            'sort',
            'direction'
        ));
    }

    /**
     * Promote / assign students to a class.
     */
    public function assignSiswa(Request $request, TahunAjaran $tahunAjaran, Kelas $kela): RedirectResponse
    {
        abort_unless($tahunAjaran->isAktif(), 403, 'Tahun ajaran tidak aktif.');
        abort_unless((int) $kela->tahun_ajaran_id === (int) $tahunAjaran->id, 404);

        $data = $request->validate([
            'siswa_ids' => ['required', 'array', 'min:1'],
            'siswa_ids.*' => ['required', 'integer', 'distinct', 'exists:siswas,id'],
            'keputusan' => ['required', 'in:naik,tinggal'],
        ]);

        $decision = $data['keputusan'];
        $now = now('Asia/Jakarta');

        $result = DB::transaction(function () use ($data, $decision, $kela, $tahunAjaran, $now) {
            $siswas = Siswa::with('kelas.tahunAjaran')->whereIn('id', $data['siswa_ids'])->lockForUpdate()->get();

            if ($siswas->count() !== count($data['siswa_ids'])) {
                throw ValidationException::withMessages(['siswa_ids' => 'Sebagian siswa tidak ditemukan.']);
            }

            foreach ($siswas as $siswa) {
                if ($siswa->status !== 'menunggu_penempatan') {
                    throw ValidationException::withMessages(['siswa_ids' => "{$siswa->nama} tidak dalam status menunggu penempatan."]);
                }

                if ($kela->tingkat === 'xi') {
                    if ($decision === 'naik') {
                        $allowedJurusan = [$siswa->kelas?->jurusan];
                        if ($siswa->kelas?->jurusan === 'mp') $allowedJurusan[] = 'mlog';
                        if ($siswa->kelas?->jurusan === 'mlog') $allowedJurusan[] = 'mp';

                        if ($siswa->kelas && ! in_array($kela->jurusan, $allowedJurusan, true)) {
                            throw ValidationException::withMessages(['siswa_ids' => "{$siswa->nama} hanya dapat ditempatkan pada kelas dengan jurusan yang sesuai."]);
                        }

                        $siswa->update(['kelas_id' => $kela->id, 'status' => 'aktif']);
                        SiswaRiwayatKelas::create([
                            'siswa_id' => $siswa->id,
                            'tahun_ajaran' => $tahunAjaran->nama,
                            'kelas_asal' => $siswa->kelas?->nama ?? 'Kelas 10',
                            'kelas_tujuan' => $kela->nama,
                            'jenis' => 'kenaikan',
                            'diproses_pada' => $now,
                        ]);
                        $this->updatePreviousYearOutcome($siswa, $tahunAjaran, $kela, 'kenaikan');
                    } else {
                        // Tinggal di kelas 10
                        $target10 = Kelas::where('tahun_ajaran_id', $tahunAjaran->id)
                            ->where('tingkat', 'x')
                            ->where('jurusan', $siswa->kelas?->jurusan)
                            ->where('rombel', $siswa->kelas?->rombel)
                            ->first() ?? Kelas::where('tahun_ajaran_id', $tahunAjaran->id)->where('tingkat', 'x')->first();

                        $targetId = $target10 ? $target10->id : $kela->id;
                        $targetNama = $target10 ? $target10->nama : $siswa->kelas?->nama ?? 'Kelas 10';

                        $siswa->update(['kelas_id' => $targetId, 'status' => 'aktif']);
                        SiswaRiwayatKelas::create([
                            'siswa_id' => $siswa->id,
                            'tahun_ajaran' => $tahunAjaran->nama,
                            'kelas_asal' => $siswa->kelas?->nama ?? 'Kelas 10',
                            'kelas_tujuan' => $targetNama,
                            'jenis' => 'penempatan',
                            'diproses_pada' => $now,
                        ]);
                        $this->updatePreviousYearOutcome($siswa, $tahunAjaran, $target10, 'penempatan');
                    }
                } elseif ($kela->tingkat === 'xii') {
                    if ($decision === 'naik') {
                        $siswa->update(['kelas_id' => $kela->id, 'status' => 'aktif']);
                        SiswaRiwayatKelas::create([
                            'siswa_id' => $siswa->id,
                            'tahun_ajaran' => $tahunAjaran->nama,
                            'kelas_asal' => $siswa->kelas?->nama ?? 'Kelas 11',
                            'kelas_tujuan' => $kela->nama,
                            'jenis' => 'kenaikan',
                            'diproses_pada' => $now,
                        ]);
                        $this->updatePreviousYearOutcome($siswa, $tahunAjaran, $kela, 'kenaikan');
                    } else {
                        // Tinggal di kelas 11
                        $target11 = Kelas::where('tahun_ajaran_id', $tahunAjaran->id)
                            ->where('tingkat', 'xi')
                            ->where('jurusan', $siswa->kelas?->jurusan)
                            ->where('rombel', $siswa->kelas?->rombel)
                            ->first() ?? Kelas::where('tahun_ajaran_id', $tahunAjaran->id)->where('tingkat', 'xi')->first();

                        $targetId = $target11 ? $target11->id : $kela->id;
                        $targetNama = $target11 ? $target11->nama : $siswa->kelas?->nama ?? 'Kelas 11';

                        $siswa->update(['kelas_id' => $targetId, 'status' => 'aktif']);
                        SiswaRiwayatKelas::create([
                            'siswa_id' => $siswa->id,
                            'tahun_ajaran' => $tahunAjaran->nama,
                            'kelas_asal' => $siswa->kelas?->nama ?? 'Kelas 11',
                            'kelas_tujuan' => $targetNama,
                            'jenis' => 'penempatan',
                            'diproses_pada' => $now,
                        ]);
                        $this->updatePreviousYearOutcome($siswa, $tahunAjaran, $target11, 'penempatan');
                    }
                } elseif ($kela->tingkat === 'x') {
                    // Siswa baru ditempatkan di kelas 10
                    $siswa->update(['kelas_id' => $kela->id, 'status' => 'aktif']);
                    SiswaRiwayatKelas::create([
                        'siswa_id' => $siswa->id,
                        'tahun_ajaran' => $tahunAjaran->nama,
                        'kelas_asal' => $siswa->kelas?->nama ?? 'Siswa Baru',
                        'kelas_tujuan' => $kela->nama,
                        'jenis' => 'penempatan',
                        'diproses_pada' => $now,
                    ]);
                }
            }

            return $siswas->count();
        }, 3);

        $msg = $decision === 'naik'
            ? "{$result} siswa berhasil ditempatkan di kelas {$kela->nama}."
            : "{$result} siswa ditetapkan tinggal di tingkat kelasnya.";

        return back()->with('success', $msg);
    }

    /**
     * Memproses kelulusan atau penahanan siswa kelas 12 dari tahun ajaran sebelumnya.
     */
    public function kelulusanKelas12(Request $request, TahunAjaran $tahunAjaran): RedirectResponse
    {
        abort_unless($tahunAjaran->isAktif(), 403, 'Tahun ajaran tidak aktif.');

        $now = now('Asia/Jakarta');
        $rolloverService = app(AcademicYearRolloverService::class);
        $isChecklistMode = $request->has('all_siswa_ids');

        if ($isChecklistMode) {
            $data = $request->validate([
                'all_siswa_ids' => ['required', 'array', 'min:1'],
                'all_siswa_ids.*' => ['required', 'integer', 'exists:siswas,id'],
                'lulus_ids' => ['nullable', 'array'],
                'lulus_ids.*' => ['integer', 'exists:siswas,id'],
            ]);

            $allIds = collect($data['all_siswa_ids'])->map(fn ($id) => (int) $id)->unique()->values();
            $lulusSet = collect($data['lulus_ids'] ?? [])->map(fn ($id) => (int) $id)->flip();

            $result = DB::transaction(function () use ($allIds, $lulusSet, $tahunAjaran, $now, $rolloverService) {
                $siswas = Siswa::with(['kelas.tahunAjaran', 'user'])->whereIn('id', $allIds)->lockForUpdate()->get();
                $lulusCount = 0;
                $tinggalCount = 0;

                foreach ($siswas as $siswa) {
                    $isLulus = $lulusSet->has($siswa->id);

                    if ($isLulus) {
                        $this->updatePreviousYearOutcome($siswa, $tahunAjaran, null, 'kelulusan');
                        $siswa->update(['status' => 'nonaktif']);

                        $siswa->pendaftarans()
                            ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
                            ->update(['status' => Pendaftaran::STATUS_LULUS]);

                        if ($siswa->user) {
                            $siswa->user->forceFill(['is_active' => false, 'remember_token' => null])->save();
                            $siswa->user->tokens()->delete();
                        }

                        $rolloverService->tutupKeanggotaanEkskul($siswa);

                        SiswaRiwayatKelas::create([
                            'siswa_id' => $siswa->id,
                            'tahun_ajaran' => $siswa->kelas?->tahunAjaran?->nama ?? $tahunAjaran->nama,
                            'kelas_asal' => $siswa->kelas?->nama ?? 'Kelas 12',
                            'kelas_tujuan' => null,
                            'jenis' => 'kelulusan',
                            'diproses_pada' => $now,
                        ]);

                        $lulusCount++;
                    } else {
                        // Tinggal di kelas 12 pada tahun ajaran aktif
                        $targetKelas12 = Kelas::where('tahun_ajaran_id', $tahunAjaran->id)
                            ->where('tingkat', 'xii')
                            ->where('jurusan', $siswa->kelas?->jurusan)
                            ->where('rombel', $siswa->kelas?->rombel)
                            ->first()
                            ?? Kelas::where('tahun_ajaran_id', $tahunAjaran->id)->where('tingkat', 'xii')->where('jurusan', $siswa->kelas?->jurusan)->first()
                            ?? Kelas::where('tahun_ajaran_id', $tahunAjaran->id)->where('tingkat', 'xii')->first();

                        if (! $targetKelas12 && $siswa->kelas) {
                            $targetKelas12 = Kelas::create([
                                'nama' => $siswa->kelas->nama,
                                'tingkat' => 'xii',
                                'jurusan' => $siswa->kelas->jurusan,
                                'rombel' => $siswa->kelas->rombel,
                                'tahun_ajaran_id' => $tahunAjaran->id,
                            ]);
                        }

                        if (! $targetKelas12) {
                            throw ValidationException::withMessages([
                                'all_siswa_ids' => "Kelas 12 tujuan tidak ditemukan di tahun ajaran aktif untuk siswa {$siswa->nama}. Buat kelas 12 di tahun ajaran aktif terlebih dahulu.",
                            ]);
                        }

                        $this->updatePreviousYearOutcome($siswa, $tahunAjaran, $targetKelas12, 'penempatan');

                        $siswa->update([
                            'kelas_id' => $targetKelas12->id,
                            'status' => 'aktif',
                        ]);

                        if ($siswa->user && ! $siswa->user->is_active) {
                            $siswa->user->forceFill(['is_active' => true])->save();
                        }

                        SiswaRiwayatKelas::create([
                            'siswa_id' => $siswa->id,
                            'tahun_ajaran' => $tahunAjaran->nama,
                            'kelas_asal' => $siswa->kelas?->nama ?? 'Kelas 12',
                            'kelas_tujuan' => $targetKelas12->nama,
                            'jenis' => 'penempatan',
                            'diproses_pada' => $now,
                        ]);

                        $tinggalCount++;
                    }
                }

                return ['lulus' => $lulusCount, 'tinggal' => $tinggalCount];
            }, 3);

            $parts = [];
            if ($result['lulus'] > 0) {
                $parts[] = "{$result['lulus']} siswa berhasil diluluskan (Alumni).";
            }
            if ($result['tinggal'] > 0) {
                $parts[] = "{$result['tinggal']} siswa ditetapkan tinggal di kelas 12 pada tahun ajaran aktif.";
            }

            $msg = ! empty($parts) ? implode(' ', $parts) : 'Data kelulusan kelas berhasil diproses.';

            return back()->with('success', $msg);
        }

        // Backward compatibility for legacy requests with siswa_ids and keputusan
        $data = $request->validate([
            'siswa_ids' => ['required', 'array', 'min:1'],
            'siswa_ids.*' => ['required', 'integer', 'exists:siswas,id'],
            'keputusan' => ['required', 'in:lulus,tinggal'],
        ]);

        $decision = $data['keputusan'];

        $count = DB::transaction(function () use ($data, $decision, $tahunAjaran, $now, $rolloverService) {
            $siswas = Siswa::with(['kelas.tahunAjaran', 'user'])->whereIn('id', $data['siswa_ids'])->lockForUpdate()->get();

            foreach ($siswas as $siswa) {
                if ($decision === 'lulus') {
                    $this->updatePreviousYearOutcome($siswa, $tahunAjaran, null, 'kelulusan');
                    $siswa->update(['status' => 'nonaktif']);

                    $siswa->pendaftarans()
                        ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
                        ->update(['status' => Pendaftaran::STATUS_LULUS]);

                    if ($siswa->user) {
                        $siswa->user->forceFill(['is_active' => false, 'remember_token' => null])->save();
                        $siswa->user->tokens()->delete();
                    }

                    $rolloverService->tutupKeanggotaanEkskul($siswa);

                    SiswaRiwayatKelas::create([
                        'siswa_id' => $siswa->id,
                        'tahun_ajaran' => $siswa->kelas?->tahunAjaran?->nama ?? $tahunAjaran->nama,
                        'kelas_asal' => $siswa->kelas?->nama ?? 'Kelas 12',
                        'kelas_tujuan' => null,
                        'jenis' => 'kelulusan',
                        'diproses_pada' => $now,
                    ]);
                } else {
                    // Tinggal di kelas 12
                    $targetKelas12 = Kelas::where('tahun_ajaran_id', $tahunAjaran->id)
                        ->where('tingkat', 'xii')
                        ->where('jurusan', $siswa->kelas?->jurusan)
                        ->where('rombel', $siswa->kelas?->rombel)
                        ->first()
                        ?? Kelas::where('tahun_ajaran_id', $tahunAjaran->id)->where('tingkat', 'xii')->where('jurusan', $siswa->kelas?->jurusan)->first()
                        ?? Kelas::where('tahun_ajaran_id', $tahunAjaran->id)->where('tingkat', 'xii')->first();

                    if (! $targetKelas12 && $siswa->kelas) {
                        $targetKelas12 = Kelas::create([
                            'nama' => $siswa->kelas->nama,
                            'tingkat' => 'xii',
                            'jurusan' => $siswa->kelas->jurusan,
                            'rombel' => $siswa->kelas->rombel,
                            'tahun_ajaran_id' => $tahunAjaran->id,
                        ]);
                    }

                    if (! $targetKelas12) {
                        throw ValidationException::withMessages(['siswa_ids' => 'Kelas 12 tujuan tidak ditemukan di tahun ajaran aktif.']);
                    }

                    $this->updatePreviousYearOutcome($siswa, $tahunAjaran, $targetKelas12, 'penempatan');

                    $siswa->update([
                        'kelas_id' => $targetKelas12->id,
                        'status' => 'aktif',
                    ]);

                    if ($siswa->user && ! $siswa->user->is_active) {
                        $siswa->user->forceFill(['is_active' => true])->save();
                    }

                    SiswaRiwayatKelas::create([
                        'siswa_id' => $siswa->id,
                        'tahun_ajaran' => $tahunAjaran->nama,
                        'kelas_asal' => $siswa->kelas?->nama ?? 'Kelas 12',
                        'kelas_tujuan' => $targetKelas12->nama,
                        'jenis' => 'penempatan',
                        'diproses_pada' => $now,
                    ]);
                }
            }

            return $siswas->count();
        }, 3);

        $msg = $decision === 'lulus'
            ? "{$count} siswa kelas 12 berhasil ditandai Lulus dan berstatus Alumni (nonaktif)."
            : "{$count} siswa kelas 12 ditetapkan Tinggal di kelas 12 pada tahun ajaran aktif.";

        return back()->with('success', $msg);
    }

    public function resolvePendingSiswa(Request $request, TahunAjaran $tahunAjaran, Kelas $kela, Siswa $siswa): RedirectResponse
    {
        abort_unless($tahunAjaran->isAktif() && (int) $kela->tahun_ajaran_id === (int) $tahunAjaran->id, 404);
        $data = $request->validate(['keputusan' => ['required', 'in:tinggal,lulus']]);

        abort_unless($siswa->status === 'menunggu_penempatan', 409);
        if ($data['keputusan'] === 'lulus' && $kela->tingkat !== 'xii') {
            throw ValidationException::withMessages(['keputusan' => 'Hanya siswa kelas 12 yang dapat ditandai lulus.']);
        }

        DB::transaction(function () use ($siswa, $kela, $tahunAjaran, $data) {
            $lulus = $data['keputusan'] === 'lulus';
            $this->updatePreviousYearOutcome($siswa, $tahunAjaran, $lulus ? null : $kela, $lulus ? 'kelulusan' : 'penempatan');
            SiswaRiwayatKelas::create([
                'siswa_id' => $siswa->id,
                'tahun_ajaran' => $tahunAjaran->nama,
                'kelas_asal' => $kela->nama,
                'kelas_tujuan' => $lulus ? null : $kela->nama,
                'jenis' => $lulus ? 'kelulusan' : 'penempatan',
                'diproses_pada' => now('Asia/Jakarta'),
            ]);

            if ($lulus) {
                $siswa->update(['status' => 'nonaktif']);
                $siswa->pendaftarans()->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])->update(['status' => Pendaftaran::STATUS_LULUS]);
                if ($siswa->user) {
                    $siswa->user->forceFill(['is_active' => false, 'remember_token' => null])->save();
                    $siswa->user->tokens()->delete();
                }
            } else {
                $siswa->update(['kelas_id' => $kela->id, 'status' => 'aktif']);
            }
        }, 3);

        return back()->with('success', $data['keputusan'] === 'lulus'
            ? "{$siswa->nama} ditandai lulus dan menjadi alumni."
            : "{$siswa->nama} ditetapkan tinggal di kelas {$kela->nama}.");
    }

    private function updatePreviousYearOutcome(Siswa $siswa, TahunAjaran $currentYear, ?Kelas $targetClass, string $type): void
    {
        $previousYear = TahunAjaran::query()
            ->where('status', 'historis')
            ->where('id', '<', $currentYear->id)
            ->orderByDesc('id')
            ->first();

        if (! $previousYear) return;

        $history = SiswaRiwayatKelas::query()
            ->where('siswa_id', $siswa->id)
            ->where('tahun_ajaran', $previousYear->nama)
            ->orderBy('id')
            ->first();

        $history?->update([
            'kelas_tujuan' => $targetClass?->nama,
            'jenis' => $type,
        ]);
    }


}
