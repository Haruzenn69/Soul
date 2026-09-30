<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Faq;
use App\Services\NotifikasiService;
use App\Support\TableKit;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    use KetuaEkskul;

    public function index(Request $request)
    {
        $ekskul = $this->ekskul();
        $base = $ekskul->faqs();

        $pendingCount = (clone $base)->where('status', Faq::STATUS_PENDING)->count();
        $total = (clone $base)->count();

        [$sort, $direction] = TableKit::sort(['pertanyaan', 'status'], 'status', 'asc');

        $faqs = (clone $base)
            ->when($request->filled('cari'), function ($query) use ($request) {
                $cari = $request->input('cari');
                $query->where(fn ($q) => $q->where('pertanyaan', 'like', "%{$cari}%")->orWhere('jawaban', 'like', "%{$cari}%"));
            })
            ->when($request->filled('status') && $request->input('status') !== 'semua', fn ($query) => $query->where('status', $request->input('status')))
            ->when($sort === 'status',
                fn ($query) => $query->when($direction === 'asc', fn ($q) => $q->orderByRaw("CASE status WHEN 'pending' THEN 1 ELSE 2 END"), fn ($q) => $q->orderByDesc('status')),
                fn ($query) => $query->orderBy($sort, $direction))
            ->paginate(10)
            ->withQueryString();

        return view('ketua.faq.index', compact('faqs', 'pendingCount', 'total', 'sort', 'direction'));
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
