<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Ekskul;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    public function index()
    {
        $ekskul = auth()->user()->siswa->pendaftarans()->where('status', 'diterima')->first()->ekskul;
        $kegiatans = Kegiatan::where('ekskul_id', $ekskul->id)->with('presensis')->get();
        return view('ketua.kegiatan.index', compact('kegiatans', 'ekskul'));
    }

    public function create()
    {
        $ekskul = auth()->user()->siswa->pendaftarans()->where('status', 'diterima')->first()->ekskul;
        return view('ketua.kegiatan.create', compact('ekskul'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'materi'             => 'required|string|max:255',
            'tanggal_kegiatan'   => 'required|date',
        ]);

        $ekskul = auth()->user()->siswa->pendaftarans()->where('status', 'diterima')->first()->ekskul;

        Kegiatan::create([
            'ekskul_id'         => $ekskul->id,
            'materi'            => $validated['materi'],
            'tanggal_kegiatan'  => $validated['tanggal_kegiatan'],
        ]);

        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function show(Kegiatan $kegiatan)
    {
        $kegiatan->load(['presensis.pendaftaran.siswa']);
        return view('ketua.kegiatan.show', compact('kegiatan'));
    }

    public function edit(Kegiatan $kegiatan)
    {
        return view('ketua.kegiatan.edit', compact('kegiatan'));
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        $validated = $request->validate([
            'materi'             => 'required|string|max:255',
            'tanggal_kegiatan'   => 'required|date',
        ]);

        $kegiatan->update($validated);

        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil diupdate.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        $kegiatan->delete();

        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil dihapus.');
    }
}