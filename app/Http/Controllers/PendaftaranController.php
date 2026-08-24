<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    private function getEkskul()
    {
        return auth()->user()->siswa->pendaftarans()->where('status', 'diterima')->first()->ekskul;
    }

    public function index()
    {
        $ekskul = $this->getEkskul();
        $pendaftarans = Pendaftaran::where('ekskul_id', $ekskul->id)
            ->with('siswa')
            ->latest('tanggal_daftar')
            ->get();

        return view('ketua.pendaftaran.index', compact('pendaftarans'));
    }

    public function show(Pendaftaran $pendaftaran)
    {
        $pendaftaran->load(['siswa', 'ekskul']);
        return view('ketua.pendaftaran.show', compact('pendaftaran'));
    }

    public function update(Request $request, Pendaftaran $pendaftaran)
    {
        $validated = $request->validate([
            'status' => 'required|in:diterima,ditolak',
        ]);

        $pendaftaran->update($validated);

        if ($validated['status'] === 'diterima') {
            $pendaftaran->siswa->update(['jabatan' => 'anggota']);
        }

        return redirect()->route('ketua.pendaftaran.index')->with('success', 'Status pendaftaran berhasil diupdate.');
    }
}
