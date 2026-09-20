<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Pendaftaran;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    use KetuaEkskul;

    public function index()
    {
        $ekskul = $this->ekskul();
        $pendaftarans = Pendaftaran::where('ekskul_id', $ekskul->id)
            ->with('siswa')
            ->latest('tanggal_daftar')
            ->get();

        return view('ketua.pendaftaran.index', compact('pendaftarans'));
    }

    public function show(Pendaftaran $pendaftaran)
    {
        $this->ensureEkskul($pendaftaran);

        $pendaftaran->load(['siswa', 'ekskul']);

        return view('ketua.pendaftaran.show', compact('pendaftaran'));
    }

    public function update(Request $request, Pendaftaran $pendaftaran)
    {
        $this->ensureEkskul($pendaftaran);

        $validated = $request->validate([
            'status' => ['required', 'in:diterima,ditolak'],
        ]);

        $pendaftaran->update($validated);

        if ($validated['status'] === Pendaftaran::STATUS_DITERIMA) {
            $pendaftaran->siswa->update(['jabatan' => 'anggota']);
            NotifikasiService::pendaftaranDiterima($pendaftaran);
        } else {
            NotifikasiService::pendaftaranDitolak($pendaftaran);
        }

        return redirect()->route('ketua.pendaftaran.index')->with('success', 'Status pendaftaran berhasil diupdate.');
    }
}
