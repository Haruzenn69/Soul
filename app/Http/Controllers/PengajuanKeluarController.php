<?php

namespace App\Http\Controllers;

use App\Models\PengajuanKeluar;
use Illuminate\Http\Request;

class PengajuanKeluarController extends Controller
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
        $pengajuanKeluars = PengajuanKeluar::where('ekskul_id', $ekskul->id)
            ->with('siswa')
            ->latest('tanggal_pengajuan')
            ->get();

        return view('ketua.pengajuan-keluar.index', compact('pengajuanKeluars'));
    }

    public function show(PengajuanKeluar $pengajuanKeluar)
    {
        $pengajuanKeluar->load(['siswa', 'ekskul']);
        return view('ketua.pengajuan-keluar.show', compact('pengajuanKeluar'));
    }

    public function update(Request $request, PengajuanKeluar $pengajuanKeluar)
    {
        $validated = $request->validate([
            'status' => 'required|in:diterima,ditolak',
        ]);

        $pengajuanKeluar->update($validated);

        if ($validated['status'] === 'diterima') {
            $pengajuanKeluar->siswa->update(['jabatan' => 'siswa']);
            \App\Models\Pendaftaran::where('siswa_id', $pengajuanKeluar->siswa_id)
                ->where('ekskul_id', $pengajuanKeluar->ekskul_id)
                ->where('status', 'diterima')
                ->update(['status' => 'nonaktif']);
        }

        return redirect()->route('ketua.pengajuan-keluar.index')->with('success', 'Pengajuan keluar berhasil diupdate.');
    }
}
