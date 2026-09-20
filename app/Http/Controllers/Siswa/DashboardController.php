<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\Presensi;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $siswa = $user->siswa;

        $pendaftaran = $siswa ? $siswa->pendaftarans()->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])->with('ekskul')->first() : null;
        $ekskul = $pendaftaran ? $pendaftaran->ekskul : null;

        $isWarned = $pendaftaran && $pendaftaran->status === Pendaftaran::STATUS_PERINGATAN;

        $statusTerakhir = $siswa ? $siswa->pendaftarans()->latest('tanggal_daftar')->first() : null;
        $isNonaktif = $siswa && $statusTerakhir && $statusTerakhir->status === Pendaftaran::STATUS_NONAKTIF && ! $pendaftaran;

        $kegiatanMendatang = $ekskul
        ? $ekskul->kegiatans()->whereDate('tanggal_kegiatan', '>=', today())->orderBy('tanggal_kegiatan', 'asc')->get()
        : collect();

        $totalHadir = $siswa && $pendaftaran
            ? Presensi::where('pendaftaran_id', $pendaftaran->id)->where('status', Presensi::STATUS_HADIR)->count()
            : 0;

        $unreadNotifCount = $siswa ? $siswa->notifikasis()->where('is_read', false)->count() : 0;

        return view('siswa.dashboard', compact('siswa', 'ekskul', 'kegiatanMendatang', 'totalHadir', 'unreadNotifCount', 'isWarned', 'isNonaktif'));
    }
}
