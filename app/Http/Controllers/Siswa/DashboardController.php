<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\Presensi;
use App\Models\Testimoni;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $siswa = $user->siswa;

        $activeEkskuls = $siswa ? $siswa->activeEkskuls() : collect();
        $hasMultipleEkskul = $activeEkskuls->count() >= 2;

        // Determine selected ekskul for switch-dashboard
        if ($hasMultipleEkskul && request('ekskul')) {
            $ledEkskul = $siswa ? $siswa->ekskulDipimpin() : null;
            if ($ledEkskul && (int) request('ekskul') === (int) $ledEkskul->id) {
                return redirect()->route('ketua.dashboard');
            }
        }

        $ekskul = $siswa ? $siswa->resolveActiveEkskul(request('ekskul') ? (int) request('ekskul') : null) : null;
        $selectedEkskulId = $ekskul?->id;
        $otherEkskul = $hasMultipleEkskul && $ekskul ? $activeEkskuls->firstWhere('id', '!=', $ekskul->id) : null;
        $pendaftaran = $siswa ? $siswa->activePendaftaranUntuk($ekskul) : null;

        // Status: peringatan for the selected ekskul only
        if ($hasMultipleEkskul && $ekskul) {
            $isWarned = $siswa && $siswa->activePendaftarans()
                ->where('ekskul_id', $ekskul->id)
                ->where('status', Pendaftaran::STATUS_PERINGATAN)
                ->exists();
        } else {
            $isWarned = $siswa && $siswa->activePendaftarans()
                ->where('status', Pendaftaran::STATUS_PERINGATAN)
                ->exists();
        }

        $statusTerakhir = $siswa ? $siswa->pendaftarans()->with('ekskul')->latest('tanggal_daftar')->first() : null;

        $nonaktifStatus = null;
        if ($siswa && $statusTerakhir && $activeEkskuls->isEmpty()) {
            if ($statusTerakhir->status === Pendaftaran::STATUS_NONAKTIF) {
                $nonaktifStatus = 'nonaktif';
            } elseif ($statusTerakhir->status === Pendaftaran::STATUS_KELUAR) {
                $nonaktifStatus = 'keluar';
            }
        }

        $isNonaktif = $nonaktifStatus !== null;
        $ekskulTerakhir = $isNonaktif ? $statusTerakhir->ekskul : null;

        // Kegiatan mendatang: filter by selected ekskul if multi
        if ($hasMultipleEkskul && $ekskul) {
            $kegiatanMendatang = \App\Models\Kegiatan::where('ekskul_id', $ekskul->id)
                ->whereDate('tanggal_kegiatan', '>=', today())
                ->with('ekskul')
                ->orderBy('tanggal_kegiatan', 'asc')
                ->take(6)
                ->get();
        } else {
            $activeEkskulIds = $activeEkskuls->pluck('id');
            $kegiatanMendatang = $activeEkskulIds->isNotEmpty()
                ? \App\Models\Kegiatan::whereIn('ekskul_id', $activeEkskulIds)
                    ->whereDate('tanggal_kegiatan', '>=', today())
                    ->with('ekskul')
                    ->orderBy('tanggal_kegiatan', 'asc')
                    ->take(6)
                    ->get()
                : collect();
        }

        // Total hadir: filter by selected ekskul if multi
        if ($hasMultipleEkskul && $ekskul && $siswa) {
            $pendaftaranId = $siswa->activePendaftaranUntuk($ekskul)?->id;
            $totalHadir = $pendaftaranId
                ? Presensi::where('pendaftaran_id', $pendaftaranId)->where('status', Presensi::STATUS_HADIR)->count()
                : 0;
        } else {
            $activePendaftaranIds = $siswa ? $siswa->activePendaftarans()->pluck('id') : collect();
            $totalHadir = $activePendaftaranIds->isNotEmpty()
                ? Presensi::whereIn('pendaftaran_id', $activePendaftaranIds)->where('status', Presensi::STATUS_HADIR)->count()
                : 0;
        }

        $unreadNotifCount = $siswa ? $siswa->notifikasis()->where('is_read', false)->count() : 0;

        $testimoniTerkirim = $siswa
            ? Testimoni::query()
                ->where('user_id', $user->id)
                ->whereIn('status', [Testimoni::STATUS_PENDING, Testimoni::STATUS_APPROVED])
                ->pluck('ekskul_id')
                ->all()
            : [];

        $hasSubmittedTestimoni = $ekskul ? in_array($ekskul->id, $testimoniTerkirim, true) : false;

        return view('siswa.dashboard', compact(
            'siswa',
            'ekskul',
            'activeEkskuls',
            'kegiatanMendatang',
            'totalHadir',
            'unreadNotifCount',
            'isWarned',
            'isNonaktif',
            'nonaktifStatus',
            'ekskulTerakhir',
            'hasSubmittedTestimoni',
            'testimoniTerkirim',
            'hasMultipleEkskul',
            'selectedEkskulId',
            'otherEkskul'
        ));
    }
}
