<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
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

        $bulan = $request->input('bulan', now()->format('Y-m'));
        if (! preg_match('/^\d{4}-\d{2}$/', $bulan)) {
            $bulan = now()->format('Y-m');
        }

        [$tahun, $bulanKe] = explode('-', $bulan);

        $presensis = $pendaftaran
            ? Presensi::where('pendaftaran_id', $pendaftaran->id)
                ->whereHas('kegiatan', fn ($k) => $k->whereYear('tanggal_kegiatan', (int) $tahun)->whereMonth('tanggal_kegiatan', (int) $bulanKe))
                ->with('kegiatan')
                ->get()
            : collect();

        $hadir = $presensis->where('status', Presensi::STATUS_HADIR)->count();
        $izin = $presensis->where('status', Presensi::STATUS_IZIN)->count();
        $sakit = $presensis->where('status', Presensi::STATUS_SAKIT)->count();
        $alpha = $presensis->where('status', Presensi::STATUS_ALPHA)->count();
        $total = $presensis->count();
        $persentaseKehadiran = $total > 0 ? round(($hadir / $total) * 100, 1) : 0;

        $presensiPerKegiatan = $presensis->keyBy('kegiatan_id');

        $kegiatans = $ekskul
            ? $ekskul->kegiatans()
                ->whereYear('tanggal_kegiatan', (int) $tahun)
                ->whereMonth('tanggal_kegiatan', (int) $bulanKe)
                ->get()
            : collect();

        $availableMonths = $pendaftaran
            ? Presensi::where('pendaftaran_id', $pendaftaran->id)
                ->whereHas('kegiatan')
                ->with('kegiatan')
                ->get()
                ->map(fn ($p) => $p->kegiatan->tanggal_kegiatan->format('Y-m'))
                ->unique()
                ->sortDesc()
                ->values()
            : collect();

        if ($availableMonths->isEmpty()) {
            $availableMonths = collect([now()->format('Y-m')]);
        }

        $unreadNotifCount = $siswa ? $siswa->notifikasis()->where('is_read', false)->count() : 0;

        return view('siswa.rekap', compact(
            'siswa',
            'ekskul',
            'presensis',
            'bulan',
            'hadir',
            'izin',
            'sakit',
            'alpha',
            'total',
            'persentaseKehadiran',
            'presensiPerKegiatan',
            'kegiatans',
            'availableMonths',
            'unreadNotifCount',
        ));
    }
}
