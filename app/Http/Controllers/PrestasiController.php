<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Prestasi;
use App\Support\TableKit;
use Illuminate\Http\Request;

class PrestasiController extends Controller
{
    use KetuaEkskul;

    public function index(Request $request)
    {
        $ekskul = $this->ekskul();
        $base = $ekskul->prestasis();
        $total = (clone $base)->count();

        $kategoriOptions = (clone $base)->select('kategori')->distinct()->pluck('kategori')->filter()->values();

        [$sort, $direction] = TableKit::sort(['judul', 'kategori', 'tahun'], 'tahun', 'desc');

        $prestasis = (clone $base)
            ->when($request->filled('cari'), function ($query) use ($request) {
                $cari = $request->input('cari');
                $query->where(fn ($q) => $q->where('judul', 'like', "%{$cari}%")->orWhere('kategori', 'like', "%{$cari}%"));
            })
            ->when($request->filled('kategori') && $request->input('kategori') !== 'semua', fn ($query) => $query->where('kategori', $request->input('kategori')))
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return view('ketua.prestasi.index', compact('prestasis', 'total', 'kategoriOptions', 'sort', 'direction'));
    }

    public function store(Request $request)
    {
        $ekskul = $this->ekskul();

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'tahun' => 'nullable|string|max:4',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('ekskul/prestasi', 'public');
        }

        $ekskul->prestasis()->create([
            'judul' => $validated['judul'],
            'kategori' => $validated['kategori'] ?? null,
            'tahun' => $validated['tahun'] ?? null,
            'foto' => $fotoPath,
        ]);

        return back()->with('success', 'Prestasi berhasil ditambahkan.');
    }

    public function destroy(Prestasi $prestasi)
    {
        $this->ensureEkskul($prestasi);
        $prestasi->delete();

        return back()->with('success', 'Prestasi berhasil dihapus.');
    }
}
