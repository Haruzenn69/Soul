<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'tingkat' => ['required', 'in:x,xi,xii'],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
        ]);

        Kelas::create($data);

        return back()->with('success', "Kelas {$data['nama']} berhasil ditambahkan.");
    }

    public function update(Request $request, Kelas $kela): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'tingkat' => ['required', 'in:x,xi,xii'],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
        ]);

        $kela->update($data);

        return back()->with('success', "Kelas {$kela->nama} berhasil diperbarui.");
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
}
