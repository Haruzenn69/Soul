<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $siswa = $user->siswa;
        $pendaftaran = $siswa ? $siswa->activePendaftaran() : null;

        if (! $pendaftaran || ! $pendaftaran->ekskul) {
            return view('ketua.dashboard', [
                'ekskul' => null,
                'totalAnggota' => 0,
                'pendingCount' => 0,
                'pengajuanCount' => 0,
                'chartKegiatan' => ['labels' => [], 'hadir' => [], 'izin' => [], 'sakit' => [], 'alpha' => []],
                'chartKelas' => ['labels' => ['Kelas X', 'Kelas XI', 'Kelas XII'], 'data' => [0, 0, 0]],
            ]);
        }

        $ekskul = $pendaftaran->ekskul;

        $kegiatanBulanIni = $ekskul->kegiatans()
            ->whereYear('tanggal_kegiatan', now()->year)
            ->whereMonth('tanggal_kegiatan', now()->month)
            ->count();

        $kegiatanTerbaru = $ekskul->kegiatans()
            ->with('presensis')
            ->orderBy('tanggal_kegiatan', 'desc')
            ->take(6)
            ->get()
            ->reverse()
            ->values();

        $chartKegiatan = [
            'labels' => $kegiatanTerbaru->map(function ($k) {
                return $k->tanggal_kegiatan ? $k->tanggal_kegiatan->translatedFormat('d M') : 'Kegiatan #'.$k->id;
            })->toArray(),
            'hadir' => $kegiatanTerbaru->map(fn ($k) => $k->presensis->where('status', 'hadir')->count())->toArray(),
            'izin' => $kegiatanTerbaru->map(fn ($k) => $k->presensis->where('status', 'izin')->count())->toArray(),
            'sakit' => $kegiatanTerbaru->map(fn ($k) => $k->presensis->where('status', 'sakit')->count())->toArray(),
            'alpha' => $kegiatanTerbaru->map(fn ($k) => $k->presensis->where('status', 'alpha')->count())->toArray(),
        ];

        $anggotaAktif = $ekskul->pendaftarans()
            ->where('status', Pendaftaran::STATUS_DITERIMA)
            ->with('siswa.kelas')
            ->get();

        $countX = $anggotaAktif->filter(fn ($p) => strtolower($p->siswa?->kelas?->tingkat ?? '') === 'x')->count();
        $countXI = $anggotaAktif->filter(fn ($p) => strtolower($p->siswa?->kelas?->tingkat ?? '') === 'xi')->count();
        $countXII = $anggotaAktif->filter(fn ($p) => strtolower($p->siswa?->kelas?->tingkat ?? '') === 'xii')->count();

        $chartKelas = [
            'labels' => ['Kelas X', 'Kelas XI', 'Kelas XII'],
            'data' => [$countX, $countXI, $countXII],
        ];

        return view('ketua.dashboard', [
            'ekskul' => $ekskul,
            'totalAnggota' => $anggotaAktif->count(),
            'pendingCount' => $ekskul->pendaftarans()->where('status', Pendaftaran::STATUS_PENDING)->count(),
            'pengajuanCount' => $ekskul->pengajuanKeluars()->where('status', 'pending')->count(),
            'chartKegiatan' => $chartKegiatan,
            'chartKelas' => $chartKelas,
            'kegiatanBulanIni' => $kegiatanBulanIni,
        ]);
    }
}
