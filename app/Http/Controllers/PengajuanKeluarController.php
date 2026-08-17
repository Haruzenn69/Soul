<?php

namespace App\Http\Controllers;

use App\Models\PengajuanKeluar;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;

class PengajuanKeluarController extends Controller
{
    public function index()
    {
        $pengajuanKeluars = PengajuanKeluar::with(['siswa', 'ekskul'])->get();
        return view('ketua.pengajuan-keluar.index', compact('pengajuanKeluars'));
    }

    public function create()
    {
        $siswa = auth()->user()->siswa;
        $pendaftaran = Pendaftaran::where('siswa_id', $siswa->id)
            ->where('status', 'diterima')
            ->first();

        return view('siswa.pengajuan-keluar.create', compact('pendaftaran'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'alasan' => 'required|string',
        ]);

        $siswa = auth()->user()->siswa;
        $pendaftaran = Pendaftaran::where('siswa_id', $siswa->id)
            ->where('status', 'diterima')
            ->first();

        PengajuanKeluar::create([
            'siswa_id'          => $siswa->id,
            'ekskul_id'         => $pendaftaran->ekskul_id,
            'alasan'            => $validated['alasan'],
            'status'            => 'pending',
            'tanggal_pengajuan' => now()->toDateString(),
        ]);

        return redirect()->route('siswa.dashboard')->with('success', 'Pengajuan keluar berhasil diajukan.');
    }

    public function show(PengajuanKeluar $pengajuanKeluar)
    {
        $pengajuanKeluar->load(['siswa', 'ekskul']);
        return view('ketua.pengajuan-keluar.show', compact('pengajuanKeluar'));
    }

    public function edit(PengajuanKeluar $pengajuanKeluar)
    {
        //
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

        return redirect()->route('pengajuan-keluar.index')->with('success', 'Pengajuan keluar berhasil diproses.');
    }

    public function destroy(PengajuanKeluar $pengajuanKeluar)
    {
        $pengajuanKeluar->delete();

        return redirect()->route('pengajuan-keluar.index')->with('success', 'Pengajuan keluar berhasil dihapus.');
    }
}