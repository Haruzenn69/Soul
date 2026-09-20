<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;

class ProfilController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        $siswa = $user->siswa;

        $pendaftaran = $siswa ? $siswa->activePendaftaran() : null;
        $pendaftaran?->load('ekskul.pembina');
        $ekskul = $pendaftaran ? $pendaftaran->ekskul : null;
        $pengajuan = $siswa ? $siswa->pengajuanKeluars()->latest('tanggal_pengajuan')->get() : collect();

        return view('profile.edit', compact('siswa', 'ekskul', 'pengajuan'));
    }
}
