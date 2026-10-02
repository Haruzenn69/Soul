<?php

namespace App\Http\Controllers\Pembina;

use App\Http\Controllers\Controller;
use App\Models\Testimoni;
use App\Services\NotifikasiService;
use App\Support\TableKit;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class TestimoniController extends Controller
{
    private function getEkskuls(): Collection
    {
        $pembina = auth()->user()?->pembina;

        if (! $pembina) {
            return collect();
        }

        return $pembina->ekskuls()->get();
    }

    private function ensureEkskul(Testimoni $testimoni): void
    {
        abort_unless(
            $this->getEkskuls()->pluck('id')->contains($testimoni->ekskul_id),
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

        $base = Testimoni::whereIn('ekskul_id', $ekskulIds)->with('ekskul');

        if ($ekskulFilter) {
            $base->where('ekskul_id', $ekskulFilter);
        }

        $pendingCount = (clone $base)->where('status', Testimoni::STATUS_PENDING)->count();
        $approvedCount = (clone $base)->where('status', Testimoni::STATUS_APPROVED)->count();
        $totalCount = (clone $base)->count();

        [$sort, $direction] = TableKit::sort(['nama', 'status', 'created_at'], 'status', 'asc');

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

        return view('pembina.testimoni.index', compact(
            'testimoniss',
            'pendingCount',
            'approvedCount',
            'totalCount',
            'ekskuls',
            'ekskulFilter',
            'sort',
            'direction'
        ));
    }

    public function store(Request $request)
    {
        $ekskuls = $this->getEkskuls();

        $validated = $request->validate([
            'ekskul_id' => ['required', 'integer'],
            'nama' => 'required|string|max:255',
            'kelas' => 'nullable|string|max:255',
            'quote' => 'required|string',
        ]);

        abort_unless(
            $ekskuls->pluck('id')->contains((int) $validated['ekskul_id']),
            403,
            'Ekskul bukan binaan Anda.'
        );

        Testimoni::create([
            'ekskul_id' => $validated['ekskul_id'],
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