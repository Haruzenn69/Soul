<?php

namespace App\Http\Controllers\Api\Ketua;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Faq;
use App\Services\NotifikasiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FaqController extends ApiController
{
    use KetuaEkskul;

    public function index(Request $request): JsonResponse
    {
        $faqs = $this->ekskul()->faqs()
            ->when($request->filled('status') && $request->input('status') !== 'semua', fn ($query) => $query->where('status', $request->input('status')))
            ->orderByRaw("CASE status WHEN 'pending' THEN 0 ELSE 1 END")
            ->latest()->paginate(25);

        return $this->ok([
            'faqs' => $faqs,
            'pending_count' => (clone $this->ekskul()->faqs())->where('status', Faq::STATUS_PENDING)->count(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'pertanyaan' => ['required', 'string', 'max:255'],
            'jawaban' => ['required', 'string', 'max:5000'],
        ]);
        $faq = $this->ekskul()->faqs()->create($data + ['status' => Faq::STATUS_ANSWERED]);

        return $this->created(['faq' => $faq], 'FAQ berhasil ditambahkan.');
    }

    public function answer(Request $request, Faq $faq): JsonResponse
    {
        $this->ensureEkskul($faq);
        $data = $request->validate(['jawaban' => ['required', 'string', 'max:5000']]);
        $faq->update($data + ['status' => Faq::STATUS_ANSWERED]);
        NotifikasiService::faqTerjawab($faq);

        return $this->ok(['faq' => $faq->fresh()], 'FAQ berhasil dijawab.');
    }

    public function destroy(Faq $faq): JsonResponse
    {
        $this->ensureEkskul($faq);
        $faq->delete();

        return $this->noContent('FAQ berhasil dihapus.');
    }
}
