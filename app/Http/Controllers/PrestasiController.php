<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Prestasi;
use Illuminate\Http\Request;

class PrestasiController extends Controller
{
    use KetuaEkskul;

    public function index()
    {
        $ekskul = $this->ekskul();
        $prestasis = $ekskul->prestasis()->latest()->get();

        return view('ketua.prestasi.index', compact('prestasis'));
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
