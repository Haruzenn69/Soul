<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
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
        $faqs = $ekskul->faqs()->latest()->get();
        return view('ketua.faq.index', compact('faqs'));
    }

    public function store(Request $request)
    {
        $ekskul = $this->getEkskul();

        $validated = $request->validate([
            'pertanyaan' => 'required|string|max:255',
            'jawaban' => 'required|string',
        ]);

        $ekskul->faqs()->create([
            'pertanyaan' => $validated['pertanyaan'],
            'jawaban' => $validated['jawaban'],
        ]);

        return back()->with('success', 'FAQ berhasil ditambahkan.');
    }

    public function destroy(Faq $faq)
    {
        abort_unless($faq->ekskul_id === $this->getEkskul()->id, 403);
        $faq->delete();
        return back()->with('success', 'FAQ berhasil dihapus.');
    }
}