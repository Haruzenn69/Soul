<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Testimoni;
use App\Services\NotifikasiService;
use App\Support\TableKit;
use Illuminate\Http\Request;

class TestimoniController extends Controller
{
    use KetuaEkskul;

    public function index(Request $request)
    {
        $ekskul = $this->ekskul();
        $base = $ekskul->testimoniss();

        $pendingCount = (clone $base)->where('status', Testimoni::STATUS_PENDING)->count();
        $total = (clone $base)->count();

        [$sort, $direction] = TableKit::sort(['nama', 'status'], 'status', 'asc');

        $testimoniss = (clone $base)
            ->when($request->filled('cari'), function ($query) use ($request) {
                $cari = $request->input('cari');
                $query->where(fn ($q) => $q->where('nama', 'like', "%{$cari}%")->orWhere('kelas', 'like', "%{$cari}%")->orWhere('quote', 'like', "%{$cari}%"));
            })
            ->when($request->filled('status') && $request->input('status') !== 'semua', fn ($query) => $query->where('status', $request->input('status')))
            ->when($sort === 'status',
                fn ($query) => $query->when($direction === 'asc', fn ($q) => $q->orderByRaw("CASE status WHEN 'pending' THEN 1 WHEN 'approved' THEN 2 ELSE 3 END"), fn ($q) => $q->orderByDesc('status')),
                fn ($query) => $query->orderBy($sort, $direction))
            ->paginate(10)
            ->withQueryString();

        return view('ketua.testimoni.index', compact('testimoniss', 'pendingCount', 'total', 'sort', 'direction'));
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
