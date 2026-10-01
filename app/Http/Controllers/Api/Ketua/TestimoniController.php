<?php

namespace App\Http\Controllers\Api\Ketua;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Http\Resources\TestimoniResource;
use App\Models\Testimoni;
use App\Services\NotifikasiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestimoniController extends ApiController
{
    use KetuaEkskul;

    public function index(Request $request): JsonResponse
    {
        $ekskul = $this->ekskul();
        $base = $ekskul->testimoniss();

        $total = (clone $base)->count();
        $pendingCount = (clone $base)->where('status', Testimoni::STATUS_PENDING)->count();
        $approvedCount = (clone $base)->where('status', Testimoni::STATUS_APPROVED)->count();
        $rejectedCount = (clone $base)->where('status', Testimoni::STATUS_REJECTED)->count();

        $query = clone $base;

        if ($cari = $request->input('cari')) {
            $query->where(fn ($q) => $q
                ->where('nama', 'like', "%{$cari}%")
                ->orWhere('kelas', 'like', "%{$cari}%")
                ->orWhere('quote', 'like', "%{$cari}%"));
        }

        if ($status = $request->input('status')) {
            if ($status !== 'semua') {
                $query->where('status', $status);
            }
        }

        $testimonis = $query->orderByRaw("CASE status WHEN 'pending' THEN 1 WHEN 'approved' THEN 2 ELSE 3 END")
            ->latest('id')
            ->paginate(15);

        return $this->ok([
            'total' => $total,
            'pending_count' => $pendingCount,
            'approved_count' => $approvedCount,
            'rejected_count' => $rejectedCount,
            'testimonis' => TestimoniResource::collection($testimonis)->resolve(),
            'pagination' => [
                'current' => $testimonis->currentPage(),
                'last' => $testimonis->lastPage(),
                'per_page' => $testimonis->perPage(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $ekskul = $this->ekskul();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kelas' => ['nullable', 'string', 'max:255'],
            'quote' => ['required', 'string', 'max:2000'],
        ]);

        $testimoni = $ekskul->testimoniss()->create([
            'nama' => $validated['nama'],
            'kelas' => $validated['kelas'] ?? null,
            'quote' => $validated['quote'],
            'status' => Testimoni::STATUS_APPROVED,
        ]);

        return $this->created(
            ['testimoni' => (new TestimoniResource($testimoni))->resolve()],
            'Testimoni berhasil ditambahkan.',
        );
    }

    public function approve(Testimoni $testimoni): JsonResponse
    {
        $this->ensureEkskul($testimoni);

        $testimoni->update(['status' => Testimoni::STATUS_APPROVED]);

        NotifikasiService::testimoniDipublish($testimoni);

        return $this->noContent('Testimoni disetujui dan kini tampil di katalog.');
    }

    public function reject(Testimoni $testimoni): JsonResponse
    {
        $this->ensureEkskul($testimoni);

        $testimoni->update(['status' => Testimoni::STATUS_REJECTED]);

        return $this->noContent('Testimoni ditolak.');
    }

    public function destroy(Testimoni $testimoni): JsonResponse
    {
        $this->ensureEkskul($testimoni);

        $testimoni->delete();

        return $this->noContent('Testimoni berhasil dihapus.');
    }
}
