<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    private function getEkskul()
    {
        return auth()->user()->siswa->pendaftarans()->where('status', 'diterima')->first()->ekskul;
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
            'tanggal_kegiatan' => 'required|date',
        ]);

        $ekskul = $this->getEkskul();

        Kegiatan::create([
            'ekskul_id' => $ekskul->id,
            'materi' => $validated['materi'],
            'tanggal_kegiatan' => $validated['tanggal_kegiatan'],
        ]);

        return redirect()->route('ketua.kegiatan.index')->with('success', 'Kegiatan berhasil dibuat.');
    }

    public function show(Kegiatan $kegiatan)
    {
        $kegiatan->load(['presensis.pendaftaran.siswa']);
        return view('ketua.kegiatan.show', compact('kegiatan'));
    }
}
