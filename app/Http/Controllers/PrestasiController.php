<?php

namespace App\Http\Controllers;

use App\Models\Prestasi;
use Illuminate\Http\Request;

class PrestasiController extends Controller
{
    private function getEkskul()
    {
        $pendaftaran = auth()->user()->siswa?->pendaftarans()->where('status', 'diterima')->first();
        abort_unless($pendaftaran, 404, 'Anda belum tergabung dalam ekskul mana pun.');
        return $pendaftaran->ekskul;
    }

    public function index()
    {
        $ekskul = $this->getEkskul();
        $prestasis = $ekskul->prestasis()->latest()->get();
        return view('ketua.prestasi.index', compact('prestasis'));
    }

    public function store(Request $request)
    {
        $ekskul = $this->getEkskul();

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
        abort_unless($prestasi->ekskul_id === $this->getEkskul()->id, 403);
        $prestasi->delete();
        return back()->with('success', 'Prestasi berhasil dihapus.');
    }
}