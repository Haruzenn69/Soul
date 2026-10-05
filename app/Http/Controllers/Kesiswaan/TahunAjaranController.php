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

        // Count pending students (from old kelas 10, waiting placement)
        $pendingSiswa = Siswa::where('status', 'menunggu_penempatan')->count();

        return view('kesiswaan.tahun-ajaran.index', compact('tahunAjarans', 'activeTA', 'activeKelas', 'pendingSiswa'));
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
                $msg .= " {$result['promoted']} siswa naik ke kelas 12, {$result['graduated']} siswa lulus, {$result['pending']} siswa menunggu penempatan kelas 11.";
            }

            return back()->with('success', $msg);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Deactivate an active tahun ajaran (becomes historis).
     */
    public function deactivate(TahunAjaran $tahunAjaran): RedirectResponse
    {
        if ($tahunAjaran->status !== 'aktif') {
            return back()->with('error', 'Hanya tahun ajaran aktif yang bisa dinonaktifkan.');
        }

        $tahunAjaran->update(['status' => 'historis']);

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

        $siswaQuery = Siswa::query()->where(function ($query) use ($kelasIds, $tahunAjaran, $kelasNames) {
            $query->whereIn('kelas_id', $kelasIds)
                ->orWhereHas('classHistories', fn ($h) => $h
                    ->where('tahun_ajaran', $tahunAjaran->nama)
                    ->whereIn('kelas_asal', $kelasNames));
        });

        if ($kelasFilter) {
            $selectedKelas = Kelas::find($kelasFilter);
            if ($selectedKelas) {
                $siswaQuery->where(function ($q) use ($selectedKelas, $tahunAjaran) {
                    $q->where('kelas_id', $selectedKelas->id)
                        ->orWhereHas('classHistories', fn ($h) => $h
                            ->where('tahun_ajaran', $tahunAjaran->nama)
                            ->where('kelas_asal', $selectedKelas->nama));
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
            ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
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
            $siswaQuery = Siswa::query()->where(function ($query) use ($kela, $tahunAjaran) {
                $query->where('kelas_id', $kela->id)
                    ->orWhereHas('classHistories', fn ($h) => $h
                        ->where('tahun_ajaran', $tahunAjaran->nama)
                        ->where('kelas_asal', $kela->nama));
            });

            $siswas = (clone $siswaQuery)
                ->when($request->filled('q'), function ($query) use ($request) {
                    $q = '%'.$request->input('q').'%';
                    $query->where(fn ($sub) => $sub->where('nama', 'like', $q)->orWhere('nis', 'like', $q));
                })
                ->orderBy($sort, $direction)
                ->paginate(20)
                ->withQueryString();

            // Load ekskul data for historical view
            $siswaIds = $siswas->pluck('id');
            $ekskulData = Pendaftaran::whereIn('siswa_id', $siswaIds)
                ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
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

        // For empty kelas 11 or penempatan: get available students from old kelas 10
        $availableSiswa = collect();
        $allPendingSiswa = collect();
        $pendingSiswaForKelas = collect();

        if ($tahunAjaran->isAktif() && $kela->tingkat === 'xi') {
            $allPendingSiswa = Siswa::where('status', 'menunggu_penempatan')
                ->with('kelas')
                ->orderBy('nama')
                ->get();

            $matching = $allPendingSiswa->filter(fn ($s) => $s->kelas?->jurusan === $kela->jurusan);
            $pendingSiswaForKelas = $matching->isNotEmpty() ? $matching : $allPendingSiswa;
            $availableSiswa = $pendingSiswaForKelas;
        }

        $stats = [
            'total' => Siswa::where('kelas_id', $kela->id)->count(),
            'laki' => Siswa::where('kelas_id', $kela->id)->where('jenis_kelamin', 'laki-laki')->count(),
            'perempuan' => Siswa::where('kelas_id', $kela->id)->where('jenis_kelamin', 'perempuan')->count(),
        ];

        return view('kesiswaan.tahun-ajaran.show', compact('tahunAjaran', 'kela', 'siswas', 'stats', 'availableSiswa', 'pendingSiswaForKelas', 'allPendingSiswa', 'sort', 'direction'));
    }

    /**
     * Assign students (from old kelas 10) to a kelas 11 in the active tahun ajaran.
     */
    public function assignSiswa(Request $request, TahunAjaran $tahunAjaran, Kelas $kela): RedirectResponse
    {
        abort_unless($tahunAjaran->isAktif(), 403, 'Tahun ajaran tidak aktif.');
        abort_unless($kela->tingkat === 'xi', 403, 'Penempatan hanya untuk kelas 11.');
        abort_unless((int) $kela->tahun_ajaran_id === (int) $tahunAjaran->id, 404);

        $data = $request->validate([
            'siswa_ids' => ['required', 'array', 'min:1'],
            'siswa_ids.*' => ['required', 'integer', 'distinct', 'exists:siswas,id'],
        ]);

        $result = DB::transaction(function () use ($data, $kela, $tahunAjaran) {
            $siswas = Siswa::with('kelas.tahunAjaran')->whereIn('id', $data['siswa_ids'])->lockForUpdate()->get();

            if ($siswas->count() !== count($data['siswa_ids'])) {
                throw ValidationException::withMessages(['siswa_ids' => 'Sebagian siswa tidak ditemukan.']);
            }

            $now = now('Asia/Jakarta');
            foreach ($siswas as $siswa) {
                if ($siswa->status !== 'menunggu_penempatan') {
                    throw ValidationException::withMessages(['siswa_ids' => "{$siswa->nama} tidak dalam status menunggu penempatan."]);
                }

                SiswaRiwayatKelas::create([
                    'siswa_id' => $siswa->id,
                    'tahun_ajaran' => $siswa->kelas?->tahunAjaran?->nama ?? '-',
                    'kelas_asal' => $siswa->kelas?->nama ?? '-',
                    'kelas_tujuan' => $kela->nama,
                    'jenis' => 'penempatan',
                    'diproses_pada' => $now,
                ]);

                $siswa->update(['kelas_id' => $kela->id, 'status' => 'aktif']);
            }

            return $siswas->count();
        }, 3);

        return back()->with('success', "{$result} siswa berhasil ditempatkan ke kelas {$kela->nama}.");
    }

    /**
     * Add a new kelas to a tahun ajaran.
     */
    public function storeKelas(Request $request, TahunAjaran $tahunAjaran): RedirectResponse
    {
        abort_unless($tahunAjaran->status !== 'historis', 403, 'Tidak bisa menambah kelas ke tahun ajaran historis.');

        $data = Validator::make($request->all(), [
            'tingkat' => ['required', 'in:'.implode(',', array_keys(config('kelas.tingkat')))],
            'jurusan' => ['required', Rule::in(array_keys(config('kelas.jurusan')))],
            'rombel' => ['required', 'integer', 'min:1'],
        ])->validate();

        $labelTingkat = config("kelas.tingkat.{$data['tingkat']}");
        $labelJurusan = config("kelas.jurusan.{$data['jurusan']}.{$data['tingkat']}");
        $nama = trim("{$labelTingkat} {$labelJurusan} {$data['rombel']}");

        $exists = Kelas::where('nama', $nama)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->exists();

        if ($exists) {
            return back()->with('error', "Kelas {$nama} sudah ada di tahun ajaran ini.");
        }

        Kelas::create([
            'nama' => $nama,
            'tingkat' => $data['tingkat'],
            'jurusan' => $data['jurusan'],
            'rombel' => $data['rombel'],
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        return back()->with('success', "Kelas {$nama} berhasil ditambahkan.");
    }

    /**
     * Delete a kelas from a tahun ajaran.
     */
    public function destroyKelas(TahunAjaran $tahunAjaran, Kelas $kela): RedirectResponse
    {
        abort_unless((int) $kela->tahun_ajaran_id === (int) $tahunAjaran->id, 404);
        abort_unless($tahunAjaran->status !== 'historis', 403);

        if ($kela->siswas()->exists()) {
            return back()->with('error', "Kelas {$kela->nama} masih memiliki siswa.");
        }

        $nama = $kela->nama;
        $kela->delete();

        return back()->with('success', "Kelas {$nama} berhasil dihapus.");
    }
}
