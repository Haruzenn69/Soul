<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Prestasi;
use App\Support\TableKit;
use Illuminate\Http\Request;

class PrestasiController extends Controller
{
    use KetuaEkskul;

    public function index(Request $request)
    {
        $user = auth()->user();
        $isKesiswaan = $user && in_array($user->role, ['kesiswaan', 'admin'], true);

        if ($isKesiswaan && ! $request->filled('ekskul')) {
            $ekskuls = \App\Models\Ekskul::withCount('prestasis')->orderBy('nama_ekskul')->get();
            $cards = $ekskuls->map(function (\App\Models\Ekskul $e) {
                $terbaru = $e->prestasis()->latest('tahun')->latest('id')->first();
                $kategoriCount = $e->prestasis()->distinct('kategori')->count('kategori');

                $stats = [
                    ['label' => 'Total Prestasi', 'value' => $e->prestasis_count.' Penghargaan', 'class' => 'font-bold text-amber-600 dark:text-amber-400'],
                    ['label' => 'Kategori Prestasi', 'value' => $kategoriCount.' Kategori', 'class' => 'font-semibold text-slate-700 dark:text-slate-300'],
                ];
                if ($terbaru) {
                    $stats[] = ['label' => 'Terbaru ('.$terbaru->tahun.')', 'value' => $terbaru->judul, 'class' => 'font-medium text-slate-500 text-[11px] truncate max-w-[130px]'];
                }

                $badge = null;
                if ($e->prestasis_count > 0) {
                    $badge = ['text' => $e->prestasis_count.' Prestasi', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300'];
                }

                return (object) [
                    'ekskul' => $e,
                    'stats' => $stats,
                    'badge' => $badge,
                    'actionText' => 'Kelola Prestasi',
                    'actionUrl' => route('ketua.prestasi.index', ['ekskul' => $e->id]),
                ];
            });

            return view('ketua.prestasi.index', [
                'isCatalog' => true,
                'cards' => $cards,
                'isKesiswaan' => true,
            ]);
        }

        $ekskul = $this->ekskul();
        $base = $ekskul->prestasis();
        $total = (clone $base)->count();

        $kategoriOptions = (clone $base)->select('kategori')->distinct()->pluck('kategori')->filter()->values();

        [$sort, $direction] = TableKit::sort(['judul', 'kategori', 'tahun'], 'tahun', 'desc');

        $prestasis = (clone $base)
            ->when($request->filled('cari'), function ($query) use ($request) {
                $cari = $request->input('cari');
                $query->where(fn ($q) => $q->where('judul', 'like', "%{$cari}%")->orWhere('kategori', 'like', "%{$cari}%"));
            })
            ->when($request->filled('kategori') && $request->input('kategori') !== 'semua', fn ($query) => $query->where('kategori', $request->input('kategori')))
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return view('ketua.prestasi.index', compact('prestasis', 'total', 'kategoriOptions', 'sort', 'direction', 'ekskul', 'isKesiswaan'));
    }

    public function store(Request $request)
    {
        $ekskul = $this->ekskul();

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'tahun' => 'nullable|string|max:4',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('ekskul/prestasi', 'public');
        }

        $ekskul->prestasis()->create([
            'judul' => $validated['judul'],
            'kategori' => $validated['kategori'] ?? null,
            'tahun' => $validated['tahun'] ?? null,
            'foto' => $fotoPath,
        ]);

        return back()->with('success', 'Prestasi berhasil ditambahkan.');
    }

    public function destroy(Prestasi $prestasi)
    {
        $this->ensureEkskul($prestasi);
        $prestasi->delete();

        return back()->with('success', 'Prestasi berhasil dihapus.');
    }
}
