<?php

namespace App\Http\Controllers\Pembina;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class FaqController extends Controller
{
    private function getEkskuls(): Collection
    {
        $pembina = auth()->user()?->pembina;

        if (! $pembina) {
            return collect();
        }

        return $pembina->ekskuls()->get();
    }

    private function ensureEkskul(Faq $faq): void
    {
        abort_unless(
            $this->getEkskuls()->pluck('id')->contains($faq->ekskul_id),
            403,
            'Data ini bukan bagian dari ekskul binaan Anda.'
        );
    }

    public function index(Request $request)
    {
        $ekskuls = $this->getEkskuls();
        $ekskulIds = $ekskuls->pluck('id');

        $ekskulFilter = $request->integer('ekskul');
        if ($ekskulFilter && ! $ekskulIds->contains($ekskulFilter)) {
            abort(403, 'Ekskul bukan binaan Anda.');
        }

        $query = Faq::whereIn('ekskul_id', $ekskulIds)
            ->with('ekskul')
            ->orderByRaw("CASE status WHEN 'pending' THEN 0 ELSE 1 END")
            ->latest();

        if ($ekskulFilter) {
            $query->where('ekskul_id', $ekskulFilter);
        }

        $faqs = $query->get();

        $pendingCount = $faqs->where('status', Faq::STATUS_PENDING)->count();
        $answeredCount = $faqs->where('status', Faq::STATUS_ANSWERED)->count();
        $totalCount = $faqs->count();

        return view('pembina.faq.index', compact(
            'faqs',
            'pendingCount',
            'answeredCount',
            'totalCount',
            'ekskuls',
            'ekskulFilter'
        ));
    }

    public function store(Request $request)
    {
        $ekskuls = $this->getEkskuls();

        $validated = $request->validate([
            'ekskul_id' => ['required', 'integer'],
            'pertanyaan' => 'required|string|max:255',
            'jawaban' => 'required|string',
        ]);

        abort_unless(
            $ekskuls->pluck('id')->contains((int) $validated['ekskul_id']),
            403,
            'Ekskul bukan binaan Anda.'
        );

        Faq::create([
            'ekskul_id' => $validated['ekskul_id'],
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