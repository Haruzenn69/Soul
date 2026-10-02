<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Services\RekapAbsensiService;
use App\Support\TableKit;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $siswa = $user->siswa;

        $pendaftaran = $siswa ? $siswa->activePendaftaran() : null;

        if (! $pendaftaran) {
            return view('siswa.presensi', [
                'presensis' => collect(),
                'siswa' => $siswa,
                'availableMonths' => collect(),
                'stats' => ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0, 'total' => 0],
                'sort' => 'tanggal',
                'direction' => 'desc',
            ]);
        }

        [$sort, $direction] = TableKit::sort(['tanggal', 'status', 'materi'], 'tanggal', 'desc');

        $baseQuery = Presensi::where('pendaftaran_id', $pendaftaran->id)
            ->with(['kegiatan.ekskul']);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'hadir' => (clone $baseQuery)->where('status', 'hadir')->count(),
            'izin'  => (clone $baseQuery)->where('status', 'izin')->count(),
            'sakit' => (clone $baseQuery)->where('status', 'sakit')->count(),
            'alpha' => (clone $baseQuery)->where('status', 'alpha')->count(),
        ];

        $kegiatanIds = (clone $baseQuery)->pluck('kegiatan_id');
        $availableMonths = Kegiatan::whereIn('id', $kegiatanIds)
            ->selectRaw('DISTINCT SUBSTRING(tanggal_kegiatan, 1, 7) as bulan')
            ->orderByDesc('bulan')
            ->pluck('bulan');

        $query = (clone $baseQuery);

        if ($request->filled('cari')) {
            $cari = $request->input('cari');
            $query->whereHas('kegiatan', fn ($k) => $k->where('materi', 'like', "%{$cari}%"));
        }

        if ($request->filled('bulan') && $request->input('bulan') !== 'semua' && str_contains($request->input('bulan'), '-')) {
            [$tahun, $bln] = explode('-', $request->input('bulan'));
            $query->whereHas('kegiatan', fn ($k) => $k->whereYear('tanggal_kegiatan', $tahun)->whereMonth('tanggal_kegiatan', $bln));
        }

        if ($request->filled('status') && $request->input('status') !== 'semua') {
            $query->where('status', $request->input('status'));
        }

        if ($sort === 'tanggal') {
            $query->orderBy(
                Kegiatan::select('tanggal_kegiatan')->whereColumn('kegiatans.id', 'presensis.kegiatan_id'),
                $direction
            );
        } elseif ($sort === 'materi') {
            $query->orderBy(
                Kegiatan::select('materi')->whereColumn('kegiatans.id', 'presensis.kegiatan_id'),
                $direction
            );
        } else {
            $query->orderBy($sort, $direction);
        }

        $presensis = $query->paginate(10)->withQueryString();

        return view('siswa.presensi', compact('presensis', 'siswa', 'availableMonths', 'stats', 'sort', 'direction'));
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