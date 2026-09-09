<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    private function getEkskul()
    {
        $pendaftaran = auth()->user()->siswa?->pendaftarans()->where('status', 'diterima')->first();
        abort_unless($pendaftaran, 404, 'Anda belum tergabung dalam ekskul mana pun.');
        return $pendaftaran->ekskul;
    }

    public function index()
    {
        $ekskul = $this->getEkskul();
        $kegiatans = Kegiatan::where('ekskul_id', $ekskul->id)
            ->withCount('presensis')
            ->latest('tanggal_kegiatan')
            ->get();

        return view('ketua.kegiatan.index', compact('kegiatans', 'ekskul'));
    }

    public function create()
    {
        return view('ketua.kegiatan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kegiatan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'dokumentasi' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $ekskul = $this->getEkskul();

        $dokumentasiPath = null;
        if ($request->hasFile('dokumentasi')) {
            $dokumentasiPath = $request->file('dokumentasi')->store('dokumentasi-kegiatan', 'public');
        }

        $kegiatan = Kegiatan::create([
            'ekskul_id' => $ekskul->id,
            'kegiatan' => $validated['kegiatan'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'dokumentasi' => $dokumentasiPath,
            'tanggal_kegiatan' => now()->toDateString(),
        ]);

        $tanggalLabel = now()->isoFormat('dddd, DD MMM Y');

        if ($ekskul->pembina) {
            Notifikasi::create([
                'pembina_id' => $ekskul->pembina->id,
                'judul' => 'Kegiatan Mendatang',
                'pesan' => 'Kegiatan baru "' . $validated['kegiatan'] . '" dijadwalkan pada ' . $tanggalLabel . ' untuk ekskul ' . $ekskul->nama_ekskul . '.',
                'tipe' => 'info',
            ]);
        }

        $anggotas = $ekskul->pendaftarans()->whereIn('status', ['diterima', 'peringatan'])->get();
        foreach ($anggotas as $anggota) {
            Notifikasi::create([
                'siswa_id' => $anggota->siswa_id,
                'judul' => 'Kegiatan Mendatang',
                'pesan' => 'Ada kegiatan "' . $validated['kegiatan'] . '" di ekskul ' . $ekskul->nama_ekskul . ' pada ' . $tanggalLabel . '. Jangan lupa hadir!',
                'tipe' => 'info',
            ]);
        }

        return redirect()->route('ketua.kegiatan.index')->with('success', 'Kegiatan berhasil dibuat.');
    }

    public function show(Kegiatan $kegiatan)
    {
        abort_unless($kegiatan->ekskul_id === $this->getEkskul()->id, 403);

        $kegiatan->load(['presensis.pendaftaran.siswa']);
        return view('ketua.kegiatan.show', compact('kegiatan'));
    }

    public function edit(Kegiatan $kegiatan)
    {
        abort_unless($kegiatan->ekskul_id === $this->getEkskul()->id, 403);

        return view('ketua.kegiatan.edit', compact('kegiatan'));
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        abort_unless($kegiatan->ekskul_id === $this->getEkskul()->id, 403);

        $validated = $request->validate([
            'kegiatan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'dokumentasi' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $dokumentasiPath = $kegiatan->dokumentasi;
        if ($request->hasFile('dokumentasi')) {
            if ($kegiatan->dokumentasi) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($kegiatan->dokumentasi);
            }
            $dokumentasiPath = $request->file('dokumentasi')->store('dokumentasi-kegiatan', 'public');
        }

        $kegiatan->update([
            'kegiatan' => $validated['kegiatan'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'dokumentasi' => $dokumentasiPath,
        ]);

        return redirect()->route('ketua.kegiatan.show', $kegiatan)
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        abort_unless($kegiatan->ekskul_id === $this->getEkskul()->id, 403);
        abort_if($kegiatan->presensis()->exists(), 422, 'Kegiatan yang sudah diisi presensinya tidak dapat dihapus.');

        if ($kegiatan->dokumentasi) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($kegiatan->dokumentasi);
        }

        $kegiatan->delete();

        return redirect()->route('ketua.kegiatan.index')->with('success', 'Kegiatan berhasil dihapus.');
    }
}
