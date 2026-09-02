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
            'materi' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'dokumentasi' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'tanggal_kegiatan' => 'required|date',
        ]);

        $ekskul = $this->getEkskul();

        $dokumentasiPath = null;
        if ($request->hasFile('dokumentasi')) {
            $dokumentasiPath = $request->file('dokumentasi')->store('dokumentasi-kegiatan', 'public');
        }

        $kegiatan = Kegiatan::create([
            'ekskul_id' => $ekskul->id,
            'materi' => $validated['materi'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'dokumentasi' => $dokumentasiPath,
            'tanggal_kegiatan' => $validated['tanggal_kegiatan'],
        ]);

        $tanggalLabel = \Carbon\Carbon::parse($validated['tanggal_kegiatan'])->isoFormat('dddd, DD MMM Y');

        if ($ekskul->pembina) {
            Notifikasi::create([
                'pembina_id' => $ekskul->pembina->id,
                'judul' => 'Kegiatan Mendatang',
                'pesan' => 'Kegiatan baru "' . $validated['materi'] . '" dijadwalkan pada ' . $tanggalLabel . ' untuk ekskul ' . $ekskul->nama_ekskul . '.',
                'tipe' => 'info',
            ]);
        }

        $anggotas = $ekskul->pendaftarans()->where('status', 'diterima')->get();
        foreach ($anggotas as $anggota) {
            Notifikasi::create([
                'siswa_id' => $anggota->siswa_id,
                'judul' => 'Kegiatan Mendatang',
                'pesan' => 'Ada kegiatan "' . $validated['materi'] . '" di ekskul ' . $ekskul->nama_ekskul . ' pada ' . $tanggalLabel . '. Jangan lupa hadir!',
                'tipe' => 'info',
            ]);
        }

        return redirect()->route('ketua.kegiatan.index')->with('success', 'Kegiatan berhasil dibuat.');
    }

    public function show(Kegiatan $kegiatan)
    {
        $kegiatan->load(['presensis.pendaftaran.siswa']);
        return view('ketua.kegiatan.show', compact('kegiatan'));
    }
}
