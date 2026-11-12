<?php

namespace App\Http\Controllers\Api\Ketua;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Http\Resources\EkskulResource;
use App\Models\Faq;
use App\Models\Pendaftaran;
use App\Models\PengajuanKeluar;
use App\Models\Testimoni;
use Illuminate\Http\JsonResponse;

class DashboardController extends ApiController
{
    use KetuaEkskul;

    public function index(): JsonResponse
    {
        $siswa = auth()->user()->siswa;
        $ekskul = $siswa?->ekskulDipimpin() ?? $siswa?->activeEkskul();

        if (! $ekskul) {
            return $this->ok([
                'ekskul' => null,
                'total_anggota' => 0,
                'anggota_baru_bulan_ini' => 0,
                'pengajuan_count' => 0,
                'testimoni_pending_count' => 0,
                'faq_pending_count' => 0,
                'kegiatan_bulan_ini' => 0,
                'chart_kegiatan' => ['labels' => [], 'hadir' => [], 'izin' => [], 'sakit' => [], 'alpha' => []],
                'chart_kelas' => ['labels' => ['Kelas 10', 'Kelas 11', 'Kelas 12'], 'data' => [0, 0, 0]],
            ]);
        }

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
            'labels' => [],
            'hadir' => [],
            'izin' => [],
            'sakit' => [],
            'alpha' => [],
        ];

        foreach ($kegiatanTerbaru as $kegiatan) {
            $chartKegiatan['labels'][] = $kegiatan->tanggal_kegiatan?->translatedFormat('d M') ?? 'Kegiatan #'.$kegiatan->id;
            $chartKegiatan['hadir'][] = $kegiatan->presensis->where('status', 'hadir')->count();
            $chartKegiatan['izin'][] = $kegiatan->presensis->where('status', 'izin')->count();
            $chartKegiatan['sakit'][] = $kegiatan->presensis->where('status', 'sakit')->count();
            $chartKegiatan['alpha'][] = $kegiatan->presensis->where('status', 'alpha')->count();
        }

        $anggotaAktif = $ekskul->pendaftarans()
            ->where('status', Pendaftaran::STATUS_DITERIMA)
            ->with('siswa.kelas')
            ->get();

        $countX = $anggotaAktif->filter(fn ($pendaftaran) => strtolower($pendaftaran->siswa?->kelas?->tingkat ?? '') === 'x')->count();
        $countXI = $anggotaAktif->filter(fn ($pendaftaran) => strtolower($pendaftaran->siswa?->kelas?->tingkat ?? '') === 'xi')->count();
        $countXII = $anggotaAktif->filter(fn ($pendaftaran) => strtolower($pendaftaran->siswa?->kelas?->tingkat ?? '') === 'xii')->count();

        $chartKelas = [
            'labels' => ['Kelas 10', 'Kelas 11', 'Kelas 12'],
            'data' => [$countX, $countXI, $countXII],
        ];

        return $this->ok([
            'ekskul' => (new EkskulResource($ekskul))->resolve(),
            'total_anggota' => $anggotaAktif->count(),
            'anggota_baru_bulan_ini' => $ekskul->pendaftarans()
                ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
                ->whereBetween('tanggal_daftar', [
                    now()->startOfMonth()->toDateString(),
                    now()->endOfMonth()->toDateString(),
                ])
                ->count(),
            'pengajuan_count' => $ekskul->pengajuanKeluars()->where('status', PengajuanKeluar::STATUS_PENDING)->count(),
            'testimoni_pending_count' => $ekskul->testimoniss()->where('status', Testimoni::STATUS_PENDING)->count(),
            'faq_pending_count' => $ekskul->faqs()->where('status', Faq::STATUS_PENDING)->count(),
            'kegiatan_bulan_ini' => $kegiatanBulanIni,
            'chart_kegiatan' => $chartKegiatan,
            'chart_kelas' => $chartKelas,
        ]);
    }
}
