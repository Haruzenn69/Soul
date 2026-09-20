<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Ekskul;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $sixMonthsAgo = now()->subMonths(5)->startOfMonth();

        $buildTrend = function ($query) use ($sixMonthsAgo) {
            $labels = [];
            $data = [];

            $rows = $query->where('created_at', '>=', $sixMonthsAgo)
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as key_month, DATE_FORMAT(created_at, '%b') as label, COUNT(*) as total")
                ->groupBy('key_month', 'label')
                ->orderBy('key_month')
                ->get();

            $byKey = $rows->pluck('total', 'key_month');

            for ($i = 5; $i >= 0; $i--) {
                $key = now()->subMonths($i)->format('Y-m');
                $labels[] = now()->subMonths($i)->isoFormat('MMM');
                $data[] = $byKey->get($key, 0);
            }

            return ['labels' => $labels, 'data' => $data];
        };

        $akunPerBulan = $buildTrend(User::query());
        $kelasPerBulan = $buildTrend(Kelas::query());
        $ekskulPerBulan = $buildTrend(Ekskul::query());

        $ekskulStatus = Ekskul::selectRaw("is_open_recruitment, COUNT(*) as total, GROUP_CONCAT(nama_ekskul SEPARATOR ', ') as nama_list")
            ->groupBy('is_open_recruitment')
            ->pluck('total', 'is_open_recruitment');

        $ekskulDetail = Ekskul::select('nama_ekskul', 'is_open_recruitment')
            ->orderBy('is_open_recruitment', 'desc')
            ->orderBy('nama_ekskul')
            ->get();

        return view('kesiswaan.dashboard', [
            'totalUsers' => User::count(),
            'totalSiswa' => Siswa::count(),
            'totalEkskul' => Ekskul::count(),
            'ekskulBuka' => Ekskul::where('is_open_recruitment', true)->count(),
            'totalKelas' => Kelas::count(),
            'akunPerBulan' => $akunPerBulan,
            'kelasPerBulan' => $kelasPerBulan,
            'ekskulPerBulan' => $ekskulPerBulan,
            'ekskulStatus' => $ekskulStatus,
            'ekskulDetail' => $ekskulDetail,
        ]);
    }
}
