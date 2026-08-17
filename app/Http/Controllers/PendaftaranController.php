<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\Ekskul;
use App\Models\Siswa;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    public function index()
    {
        $pendaftarans = Pendaftaran::with(['siswa', 'ekskul'])->get();
        return view('ketua.pendaftaran.index', compact('pendaftarans'));
    }

    public function create()
    {
        $ekskuls = Ekskul::where('is_open_recruitment', true)->get();
        return view('siswa.pendaftaran.create', compact('ekskuls'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ekskul_id' => 'required|exists:ekskuls,id',
        ]);

        $siswa = auth()->user()->siswa;

        $exists = Pendaftaran::where('siswa_id', $siswa->id)->exists();
        if ($exists) {
            return back()->with('error', 'Anda sudah terdaftar di satu ekstrakurikuler.');
        }

        Pendaftaran::create([
            'siswa_id'      => $siswa->id,
            'ekskul_id'     => $validated['ekskul_id'],
            'tanggal_daftar'=> now()->toDateString(),
            'status'        => 'pending',
        ]);

        return redirect()->route('siswa.dashboard')->with('success', 'Pendaftaran berhasil diajukan.');
    }

    public function show(Pendaftaran $pendaftaran)
    {
        $pendaftaran->load(['siswa', 'ekskul', 'presensis.kegiatan']);
        return view('ketua.pendaftaran.show', compact('pendaftaran'));
    }

    public function edit(Pendaftaran $pendaftaran)
    {
        //
    }

    public function update(Request $request, Pendaftaran $pendaftaran)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,diterima',
        ]);

        $pendaftaran->update($validated);

        if ($validated['status'] === 'diterima') {
            $pendaftaran->siswa->update(['jabatan' => 'anggota']);
        }

        return redirect()->route('pendaftaran.index')->with('success', 'Status pendaftaran berhasil diupdate.');
    }

    public function destroy(Pendaftaran $pendaftaran)
    {
        $pendaftaran->delete();

        return redirect()->route('pendaftaran.index')->with('success', 'Pendaftaran berhasil dihapus.');
    }
}