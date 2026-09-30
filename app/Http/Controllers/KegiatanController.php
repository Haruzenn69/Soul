<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Kegiatan;
use App\Services\NotifikasiService;
use App\Support\TableKit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KegiatanController extends Controller
{
    use KetuaEkskul;

    public function index(Request $request)
    {
        $ekskul = $this->ekskul();
        $base = Kegiatan::where('ekskul_id', $ekskul->id);
        $total = (clone $base)->count();

        [$sort, $direction] = TableKit::sort(['tanggal_kegiatan', 'materi'], 'tanggal_kegiatan', 'desc');

        $kegiatans = (clone $base)
            ->when($request->filled('cari'), function ($query) use ($request) {
                $cari = $request->input('cari');
                $query->where(function ($sub) use ($cari) {
                    $sub->where('materi', 'like', '%'.$cari.'%')
                        ->orWhere('deskripsi', 'like', '%'.$cari.'%');
                });
            })
            ->when($request->filled('jenis') && $request->input('jenis') !== 'semua', function ($query) use ($request) {
                if ($request->input('jenis') === 'event') {
                    $query->whereNotNull('jenis_kegiatan');
                } else {
                    $query->whereNull('jenis_kegiatan');
                }
            })
            ->withCount('presensis')
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return view('ketua.kegiatan.index', compact('kegiatans', 'ekskul', 'total', 'sort', 'direction'));
    }

    public function create()
    {
        return view('ketua.kegiatan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'materi' => 'required|string|max:255',
            'jenis_kegiatan' => 'nullable|in:event',
            'tanggal_kegiatan' => 'required|date',
            'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_kegiatan',
            'deskripsi' => 'nullable|string',
            'dokumentasi' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $ekskul = $this->ekskul();

        $dokumentasiPath = null;
        if ($request->hasFile('dokumentasi')) {
            $dokumentasiPath = $request->file('dokumentasi')->store('dokumentasi-kegiatan', 'public');
        }

        $kegiatan = Kegiatan::create([
            'ekskul_id' => $ekskul->id,
            'materi' => $validated['materi'],
            'jenis_kegiatan' => $validated['jenis_kegiatan'] ?? null,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'dokumentasi' => $dokumentasiPath,
            'tanggal_kegiatan' => $validated['tanggal_kegiatan'],
            'tanggal_berakhir' => $validated['tanggal_berakhir'] ?? null,
        ]);

        NotifikasiService::kegiatanDibuat($kegiatan);

        return redirect()->route('ketua.kegiatan.index')->with('success', 'Kegiatan berhasil dibuat.');
    }

    public function show(Kegiatan $kegiatan)
    {
        $this->ensureEkskul($kegiatan);

        $kegiatan->load(['presensis.pendaftaran.siswa', 'presensiPelatihs', 'ekskul.pelatih']);

        $presensiPelatih = $kegiatan->presensiPelatihs->first();

        return view('ketua.kegiatan.show', compact('kegiatan', 'presensiPelatih'));
    }

    public function edit(Kegiatan $kegiatan)
    {
        $this->ensureEkskul($kegiatan);

        return view('ketua.kegiatan.edit', compact('kegiatan'));
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        $this->ensureEkskul($kegiatan);

        $validated = $request->validate([
            'materi' => 'required|string|max:255',
            'jenis_kegiatan' => 'nullable|in:event',
            'tanggal_kegiatan' => 'required|date',
            'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_kegiatan',
            'deskripsi' => 'nullable|string',
            'dokumentasi' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $dokumentasiPath = $kegiatan->dokumentasi;
        if ($request->hasFile('dokumentasi')) {
            if ($kegiatan->dokumentasi) {
                Storage::disk('public')->delete($kegiatan->dokumentasi);
            }
            $dokumentasiPath = $request->file('dokumentasi')->store('dokumentasi-kegiatan', 'public');
        }

        $kegiatan->update([
            'materi' => $validated['materi'],
            'jenis_kegiatan' => $validated['jenis_kegiatan'] ?? null,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'dokumentasi' => $dokumentasiPath,
            'tanggal_kegiatan' => $validated['tanggal_kegiatan'],
            'tanggal_berakhir' => $validated['tanggal_berakhir'] ?? null,
        ]);

        return redirect()->route('ketua.kegiatan.show', $kegiatan)
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        $this->ensureEkskul($kegiatan);

        if ($kegiatan->dokumentasi) {
            Storage::disk('public')->delete($kegiatan->dokumentasi);
        }

        $kegiatan->delete();

        return redirect()->route('ketua.kegiatan.index')->with('success', 'Kegiatan berhasil dihapus.');
    }
}
