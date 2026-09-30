<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KelasController extends Controller
{
    public function index(Request $request): View
    {
        $currentTingkat = $request->input('tingkat');
        $currentTahunAjaranId = $request->input('tahun_ajaran_id');
        $q = $request->input('q');

        $kelas = Kelas::with(['tahunAjaran', 'siswas'])
            ->when($q, function ($query) use ($q) {
                $query->where('nama', 'like', '%'.$q.'%');
            })
            ->when($currentTingkat, fn ($query) => $query->where('tingkat', $currentTingkat))
            ->when($currentTahunAjaranId, fn ($query) => $query->where('tahun_ajaran_id', $currentTahunAjaranId))
            ->orderBy('nama')
            ->paginate(12)
            ->withQueryString();

        $counts = [
            'all' => Kelas::count(),
            'x' => Kelas::where('tingkat', 'x')->count(),
            'xi' => Kelas::where('tingkat', 'xi')->count(),
            'xii' => Kelas::where('tingkat', 'xii')->count(),
            'total_siswa' => \App\Models\Siswa::whereNotNull('kelas_id')->count(),
        ];

        return view('kesiswaan.kelas.index', [
            'kelas' => $kelas,
            'counts' => $counts,
            'tahunAjarans' => TahunAjaran::orderBy('nama', 'desc')->get(),
            'activeTingkat' => $currentTingkat,
        ]);
    }

    public function show(Request $request, Kelas $kela): View
    {
        $kela->load(['tahunAjaran']);

        $sort = in_array($request->input('sort'), ['nama', 'nis', 'created_at'], true) ? $request->input('sort') : 'nama';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        $siswas = $kela->siswas()
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%'.$request->input('q').'%';
                $query->where(function ($sub) use ($q) {
                    $sub->where('nama', 'like', $q)
                        ->orWhere('nis', 'like', $q)
                        ->orWhere('email', 'like', $q);
                });
            })
            ->when($request->filled('jenis_kelamin'), fn ($query) => $query->where('jenis_kelamin', $request->input('jenis_kelamin')))
            ->when($request->filled('jabatan'), fn ($query) => $query->where('jabatan', $request->input('jabatan')))
            ->orderBy($sort, $direction)
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => $kela->siswas()->count(),
            'laki' => $kela->siswas()->where('jenis_kelamin', 'laki-laki')->count(),
            'perempuan' => $kela->siswas()->where('jenis_kelamin', 'perempuan')->count(),
            'ketua' => $kela->siswas()->where('jabatan', 'ketua')->count(),
        ];

        return view('kesiswaan.kelas.show', [
            'kelas' => $kela,
            'siswas' => $siswas,
            'stats' => $stats,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $nama = $this->buildNama($data['tingkat'], $data['jurusan'], $data['rombel']);

        if ($this->kelasExists($nama, $data['tahun_ajaran_id'])) {
            return back()->with('error', "Kelas {$nama} sudah ada pada tahun ajaran ini.");
        }

        Kelas::create([
            'nama' => $nama,
            'tingkat' => $data['tingkat'],
            'jurusan' => $data['jurusan'],
            'rombel' => $data['rombel'],
            'tahun_ajaran_id' => $data['tahun_ajaran_id'],
        ]);

        return back()->with('success', "Kelas {$nama} berhasil ditambahkan.");
    }

    public function update(Request $request, Kelas $kela): RedirectResponse
    {
        $data = $this->validatedData($request);

        $nama = $this->buildNama($data['tingkat'], $data['jurusan'], $data['rombel']);

        if ($this->kelasExists($nama, $data['tahun_ajaran_id'], $kela->id)) {
            return back()->with('error', "Kelas {$nama} sudah ada pada tahun ajaran ini.");
        }

        $kela->update([
            'nama' => $nama,
            'tingkat' => $data['tingkat'],
            'jurusan' => $data['jurusan'],
            'rombel' => $data['rombel'],
            'tahun_ajaran_id' => $data['tahun_ajaran_id'],
        ]);

        return back()->with('success', "Kelas {$nama} berhasil diperbarui.");
    }

    public function destroy(Kelas $kela): RedirectResponse
    {
        if ($kela->siswas()->exists()) {
            return back()->with('error', "Kelas {$kela->nama} masih memiliki siswa dan tidak bisa dihapus.");
        }

        $nama = $kela->nama;
        $kela->delete();

        return back()->with('success', "Kelas {$nama} berhasil dihapus.");
    }

    private function validatedData(Request $request): array
    {
        return Validator::make($request->all(), [
            'tingkat' => ['required', 'in:'.implode(',', array_keys(config('kelas.tingkat')))],
            'jurusan' => ['required', Rule::in(array_keys(config('kelas.jurusan')))],
            'rombel' => ['required', 'integer', 'min:1'],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
        ])->validate();
    }

    private function buildNama(string $tingkat, string $jurusan, string $rombel): string
    {
        $labelTingkat = config("kelas.tingkat.{$tingkat}", $tingkat);
        $labelJurusan = config("kelas.jurusan.{$jurusan}.{$tingkat}", $jurusan);

        return trim("{$labelTingkat} {$labelJurusan} {$rombel}");
    }

    private function kelasExists(string $nama, int $tahunAjaranId, ?int $ignoreId = null): bool
    {
        return Kelas::query()
            ->where('nama', $nama)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists();
    }
}
