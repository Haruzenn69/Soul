<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\EkskulResource;
use App\Http\Resources\EkskulDetailResource;
use App\Http\Resources\FaqResource;
use App\Http\Resources\TestimoniResource;
use App\Models\Ekskul;
use App\Models\Faq;
use App\Models\Testimoni;
use App\Services\NotifikasiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Ekskul::query()
            ->with(['pembina', 'pelatih'])
            ->where('status', true);

        if ($cari = $request->input('cari')) {
            $query->where(fn ($q) => $q
                ->where('nama_ekskul', 'like', "%{$cari}%")
                ->orWhereHas('pembina', fn ($p) => $p->where('nama', 'like', "%{$cari}%")));
        }

        if ($request->boolean('open_only')) {
            $query->where('is_open_recruitment', true);
        }

        $ekskuls = $query->withCount([
            'pendaftarans as anggota_count' => fn ($q) => $q->whereIn('status', ['diterima', 'peringatan']),
        ])->get();

        return $this->ok([
            'ekskuls' => EkskulResource::collection($ekskuls),
        ]);
    }

    public function show(Ekskul $ekskul): JsonResponse
    {
        $ekskul->load([
            'pembina',
            'pelatih',
            'kegiatans' => fn ($q) => $q->latest('tanggal_kegiatan')->take(6),
            'prestasis',
            'testimoniss' => fn ($q) => $q->where('status', Testimoni::STATUS_APPROVED),
            'faqs' => fn ($q) => $q->where('status', Faq::STATUS_ANSWERED),
            'pendaftarans' => fn ($q) => $q->whereIn('status', ['diterima', 'peringatan']),
            'galeris',
        ]);

        return $this->ok([
            'ekskul' => (new EkskulDetailResource($ekskul))->resolve(),
        ]);
    }

    public function storeTestimoni(Request $request, Ekskul $ekskul): JsonResponse
    {
        $this->authorizeCollector($request);
        $user = $request->user();

        $validated = $request->validate([
            'quote' => ['required', 'string', 'max:2000'],
        ], [
            'quote.required' => 'Isi testimoni wajib diisi.',
            'quote.max' => 'Testimoni maksimal 2000 karakter.',
        ]);

        $exists = $ekskul->testimoniss()
            ->where('user_id', $user->id)
            ->whereIn('status', [Testimoni::STATUS_PENDING, Testimoni::STATUS_APPROVED])
            ->exists();

        if ($exists) {
            return $this->error('Kamu sudah mengirim testimoni untuk ekskul ini.', 422);
        }

        [$nama, $kelas] = $this->contributorIdentity($user);

        $testimoni = $ekskul->testimoniss()->create([
            'user_id' => $user->id,
            'nama' => $nama,
            'kelas' => $kelas,
            'quote' => $validated['quote'],
            'status' => Testimoni::STATUS_PENDING,
        ]);

        NotifikasiService::testimoniDiajukan($testimoni);

        return $this->created(
            ['testimoni' => (new TestimoniResource($testimoni))->resolve()],
            'Testimoni terkirim. Menunggu persetujuan ketua ekskul.',
        );
    }

    public function storeFaq(Request $request, Ekskul $ekskul): JsonResponse
    {
        $this->authorizeCollector($request);

        $validated = $request->validate([
            'pertanyaan' => ['required', 'string', 'max:255'],
        ], [
            'pertanyaan.required' => 'Pertanyaan wajib diisi.',
            'pertanyaan.max' => 'Pertanyaan maksimal 255 karakter.',
        ]);

        $faq = $ekskul->faqs()->create([
            'user_id' => $request->user()->id,
            'pertanyaan' => $validated['pertanyaan'],
            'jawaban' => null,
            'status' => Faq::STATUS_PENDING,
        ]);

        NotifikasiService::faqDiajukan($faq);

        return $this->created(
            ['faq' => (new FaqResource($faq))->resolve()],
            'Pertanyaan terkirim. Ketua ekskul akan menjawabnya.',
        );
    }

    private function authorizeCollector(Request $request): void
    {
        if ($request->user()->role === 'kesiswaan') {
            abort(403, 'Fitur ini tidak tersedia untuk akun kesiswaan.');
        }
    }

    private function contributorIdentity($user): array
    {
        if ($user->siswa) {
            return [$user->siswa->nama, $user->siswa->kelas?->nama];
        }

        if ($user->pembina) {
            return [$user->pembina->nama, null];
        }

        return [$user->username, null];
    }
}