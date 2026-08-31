<?php

namespace App\Http\Controllers;

use App\Models\Testimoni;
use Illuminate\Http\Request;

class TestimoniController extends Controller
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
        $testimoniss = $ekskul->testimoniss()->latest()->get();
        return view('ketua.testimoni.index', compact('testimoniss'));
    }

    public function store(Request $request)
    {
        $ekskul = $this->getEkskul();

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kelas' => 'nullable|string|max:255',
            'quote' => 'required|string',
        ]);

        $ekskul->testimoniss()->create([
            'nama' => $validated['nama'],
            'kelas' => $validated['kelas'] ?? null,
            'quote' => $validated['quote'],
        ]);

        return back()->with('success', 'Testimoni berhasil ditambahkan.');
    }

    public function destroy(Testimoni $testimoni)
    {
        abort_unless($testimoni->ekskul_id === $this->getEkskul()->id, 403);
        $testimoni->delete();
        return back()->with('success', 'Testimoni berhasil dihapus.');
    }
}