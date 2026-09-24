<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Testimoni;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;

class TestimoniController extends Controller
{
    use KetuaEkskul;

    public function index()
    {
        $ekskul = $this->ekskul();
        $testimoniss = $ekskul->testimoniss()
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->latest()
            ->get();

        $pendingCount = $testimoniss->where('status', Testimoni::STATUS_PENDING)->count();

        return view('ketua.testimoni.index', compact('testimoniss', 'pendingCount'));
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
            'status' => Testimoni::STATUS_APPROVED,
        ]);

        return back()->with('success', 'Testimoni berhasil ditambahkan.');
    }

    public function approve(Testimoni $testimoni)
    {
        $this->ensureEkskul($testimoni);

        $testimoni->update(['status' => Testimoni::STATUS_APPROVED]);

        NotifikasiService::testimoniDipublish($testimoni);

        return back()->with('success', 'Testimoni disetujui dan sudah tampil di katalog.');
    }

    public function reject(Testimoni $testimoni)
    {
        $this->ensureEkskul($testimoni);

        $testimoni->update(['status' => Testimoni::STATUS_REJECTED]);

        return back()->with('success', 'Testimoni ditolak.');
    }

    public function destroy(Testimoni $testimoni)
    {
        $this->ensureEkskul($testimoni);
        $testimoni->delete();

        return back()->with('success', 'Testimoni berhasil dihapus.');
    }
}
