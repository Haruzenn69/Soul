<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    use KetuaEkskul;

    public function index()
    {
        $ekskul = $this->ekskul();
        $faqs = $ekskul->faqs()->latest()->get();

        return view('ketua.faq.index', compact('faqs'));
    }

    public function store(Request $request)
    {
        $ekskul = $this->ekskul();

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
        $this->ensureEkskul($faq);
        $faq->delete();

        return back()->with('success', 'FAQ berhasil dihapus.');
    }
}
