<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Testimoni;
use Illuminate\Http\Request;

class TestimoniController extends Controller
{
    use KetuaEkskul;

    public function index()
    {
        $ekskul = $this->ekskul();
        $testimoniss = $ekskul->testimoniss()->latest()->get();

        return view('ketua.testimoni.index', compact('testimoniss'));
    }

    public function store(Request $request)
    {
        $ekskul = $this->ekskul();

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
        $this->ensureEkskul($testimoni);
        $testimoni->delete();

        return back()->with('success', 'Testimoni berhasil dihapus.');
    }
}
