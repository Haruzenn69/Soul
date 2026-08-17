<?php

namespace App\Http\Controllers;

use App\Models\PengajuanKeluar;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;

class PengajuanKeluarController extends Controller
{
    private function getEkskul()
    {
        return auth()->user()->siswa->pendaftarans()->where('status', 'diterima')->first()->ekskul;
    }

    public function index()
    {
        $ekskul = $this->getEkskul();
        $pengajuanKeluars = PengajuanKeluar::where('ekskul_id', $ekskul->id)
            ->with('siswa')
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

            Pendaftaran::where('siswa_id', $pengajuanKeluar->siswa_id)
                ->where('ekskul_id', $pengajuanKeluar->ekskul_id)
                ->update(['status' => 'pending']);
        }

        return redirect()->route('ketua.pengajuan-keluar.index')->with('success', 'Pengajuan keluar berhasil diproses.');
    }
}
