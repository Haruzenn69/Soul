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
        $kelas = Kelas::with(['tahunAjaran', 'siswas'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where('nama', 'like', '%'.$request->input('q').'%');
            })
            ->when($request->filled('tingkat'), fn ($query) => $query->where('tingkat', $request->input('tingkat')))
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('kesiswaan.kelas.index', [
            'kelas' => $kelas,
            'tahunAjarans' => TahunAjaran::orderBy('nama', 'desc')->get(),
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
