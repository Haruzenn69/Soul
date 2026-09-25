<?php

namespace App\Http\Controllers;

use App\Models\Ekskul;
use App\Models\Faq;
use App\Models\Testimoni;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;

class EkskulCatalogController extends Controller
{
    public function show(Ekskul $ekskul)
    {
        $ekskul->load([
            'pembina',
            'pelatih',
            'kegiatans' => fn($q) => $q->latest('tanggal_kegiatan')->take(6),
            'prestasis',
            'testimoniss' => fn($q) => $q->where('status', Testimoni::STATUS_APPROVED),
            'faqs' => fn($q) => $q->where('status', Faq::STATUS_ANSWERED),
            'pendaftarans' => fn($q) => $q->whereIn('status', ['diterima', 'peringatan']),
            'galeris',
        ]);

        $totalAnggota = $ekskul->pendaftarans->count();
        $galeris = $ekskul->galeris->pluck('foto');

        $hasSubmittedTestimoni = auth()->check()
            ? $ekskul->testimoniss()
                ->where('user_id', auth()->id())
                ->whereIn('status', [Testimoni::STATUS_PENDING, Testimoni::STATUS_APPROVED])
                ->exists()
            : false;

        return view('ekskul.detail', compact('ekskul', 'totalAnggota', 'galeris', 'hasSubmittedTestimoni'));
    }

    public function storeTestimoni(Request $request, Ekskul $ekskul)
    {
        $this->authorizeContributor();

        $validated = $request->validate([
            'quote' => 'required|string|max:2000',
        ]);

        $user = auth()->user();

        $already = $ekskul->testimoniss()
            ->where('user_id', $user->id)
            ->whereIn('status', [Testimoni::STATUS_PENDING, Testimoni::STATUS_APPROVED])
            ->exists();

        abort_if($already, 422, 'Kamu sudah mengirim testimoni untuk ekskul ini.');

        [$nama, $kelas] = $this->contributorIdentity($user);

        $testimoni = $ekskul->testimoniss()->create([
            'user_id' => $user->id,
            'nama' => $nama,
            'kelas' => $kelas,
            'quote' => $validated['quote'],
            'status' => Testimoni::STATUS_PENDING,
        ]);

        NotifikasiService::testimoniDiajukan($testimoni);

        return back()->with('success', 'Testimoni terkirim. Menunggu persetujuan ketua ekskul.');
    }

    public function storeFaq(Request $request, Ekskul $ekskul)
    {
        $this->authorizeContributor();

        $validated = $request->validate([
            'pertanyaan' => 'required|string|max:255',
        ]);

        $faq = $ekskul->faqs()->create([
            'user_id' => auth()->id(),
            'pertanyaan' => $validated['pertanyaan'],
            'jawaban' => null,
            'status' => Faq::STATUS_PENDING,
        ]);

        NotifikasiService::faqDiajukan($faq);

        return back()->with('success', 'Pertanyaan terkirim. Ketua ekskul akan menjawabnya.');
    }

    private function authorizeContributor(): void
    {
        abort_unless(auth()->user()?->role !== 'kesiswaan', 403, 'Fitur ini tidak tersedia untuk akun kesiswaan.');
    }

    /**
     * @return array{0: string, 1: ?string}
     */
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