<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Services\RekapAbsensiService;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $siswa = $user->siswa;

        $pendaftaran = $siswa ? $siswa->activePendaftaran() : null;
        $presensis = $pendaftaran
            ? Presensi::where('pendaftaran_id', $pendaftaran->id)->with('kegiatan')
                ->when($request->filled('cari'), function ($query) use ($request) {
                    $cari = $request->input('cari');
                    $query->whereHas('kegiatan', fn ($k) => $k->where('materi', 'like', "%{$cari}%"));
                })
                ->when($request->filled('bulan'), function ($query) use ($request) {
                    [$tahun, $bulan] = explode('-', $request->input('bulan'));
                    $query->whereHas('kegiatan', fn ($k) => $k->whereYear('tanggal_kegiatan', $tahun)->whereMonth('tanggal_kegiatan', $bulan));
                })
                ->get()
            : collect();

        return view('siswa.presensi', compact('presensis', 'siswa'));
    }

    public function rekap(Request $request)
    {
        $user = auth()->user();
        $siswa = $user->siswa;

        $pendaftaran = $siswa ? $siswa->activePendaftaran() : null;
        $ekskul = $pendaftaran?->ekskul;

        $service = app(RekapAbsensiService::class);
        $bulan = $service->normalizeBulan($request->input('bulan'));

        $data = $ekskul ? $service->rekap($ekskul, $bulan) : [
            'ekskul' => null,
            'bulan' => $bulan,
            'kegiatans' => collect(),
            'rows' => collect(),
            'totalHadir' => 0,
            'totalIzin' => 0,
            'totalSakit' => 0,
            'totalAlpha' => 0,
            'availableMonths' => collect([now()->format('Y-m')]),
        ];

        $rekapSiswa = $data['rows']->firstWhere('pendaftaran.id', $pendaftaran?->id);

        $unreadNotifCount = $siswa ? $siswa->notifikasis()->where('is_read', false)->count() : 0;

        return view('siswa.rekap', array_merge($data, [
            'siswa' => $siswa,
            'pendaftaran' => $pendaftaran,
            'rekapSiswa' => $rekapSiswa,
            'unreadNotifCount' => $unreadNotifCount,
        ]));
    }
}