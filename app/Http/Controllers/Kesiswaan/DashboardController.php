<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Ekskul;
use App\Models\Kelas;
use App\Models\Notifikasi;
use App\Models\Pendaftaran;
use App\Models\Siswa;
use App\Models\TahunAjaran;
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

        // Notifications for dashboard
        $notifikasis = Notifikasi::where('user_id', auth()->id())
            ->latest()
            ->take(5)
            ->get();

        $unreadCount = Notifikasi::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        // Pending pendaftaran
        $pendingPendaftaran = Pendaftaran::where('status', Pendaftaran::STATUS_PENDING)->count();

        // Active tahun ajaran
        $tahunAjaranAktif = TahunAjaran::getActive();

        return view('kesiswaan.dashboard', [
            'totalUsers' => User::count(),
            'totalSiswa' => Siswa::whereIn('status', ['aktif', 'menunggu_penempatan'])->count(),
            'totalEkskul' => Ekskul::count(),
            'ekskulBuka' => Ekskul::where('status', true)->count(),
            'totalKelas' => Kelas::count(),
            'akunPerBulan' => $akunPerBulan,
            'kelasPerBulan' => $kelasPerBulan,
            'ekskulPerBulan' => $ekskulPerBulan,
            'notifikasis' => $notifikasis,
            'unreadCount' => $unreadCount,
            'pendingPendaftaran' => $pendingPendaftaran,
            'tahunAjaranAktif' => $tahunAjaranAktif,
        ]);
    }
}
