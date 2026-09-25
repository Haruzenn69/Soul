<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Faq;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    use KetuaEkskul;

    public function index()
    {
        $ekskul = $this->ekskul();
        $faqs = $ekskul->faqs()
            ->orderByRaw("FIELD(status, 'pending', 'answered')")
            ->latest()
            ->get();

        $pendingCount = $faqs->where('status', Faq::STATUS_PENDING)->count();

        return view('ketua.faq.index', compact('faqs', 'pendingCount'));
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
            'status' => Faq::STATUS_ANSWERED,
        ]);

        return back()->with('success', 'FAQ berhasil ditambahkan.');
    }

    public function answer(Request $request, Faq $faq)
    {
        $this->ensureEkskul($faq);

        $validated = $request->validate([
            'jawaban' => 'required|string',
        ]);

        $faq->update([
            'jawaban' => $validated['jawaban'],
            'status' => Faq::STATUS_ANSWERED,
        ]);

        NotifikasiService::faqTerjawab($faq);

        return back()->with('success', 'FAQ dijawab dan sudah tampil di katalog.');
    }

    public function destroy(Faq $faq)
    {
        $this->ensureEkskul($faq);
        $faq->delete();

        return back()->with('success', 'FAQ berhasil dihapus.');
    }
}
