<?php

namespace App\Services;

use App\Models\Ekskul;
use App\Models\Kegiatan;
use App\Models\Pendaftaran;
use App\Models\Presensi;
use App\Models\PresensiPelatih;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Sumber bersama perhitungan rekap absensi untuk ketua, pembina, dan siswa
 * sehingga angka yang tampil di ketiga peran selalu identik.
 */
class RekapAbsensiService
{
    /**
     * Normalisasi input bulan menjadi format Y-m yang valid.
     */
    public function normalizeBulan(?string $bulan): string
    {
        $bulan = $bulan ?: now()->format('Y-m');

        if (! preg_match('/^\d{4}-\d{2}$/', $bulan)) {
            return now()->format('Y-m');
        }

        return $bulan;
    }

    /**
     * Bangun data rekap absensi untuk satu ekskul pada satu bulan.
     *
     * Kegiatan rutin dihitung ke persentase kehadiran, kegiatan event
     * (mis. diklat) hanya ditampilkan di bagian terpisah dan tidak dihitung.
     *
     * @return array{
     *     ekskul: Ekskul,
     *     bulan: string,
     *     kegiatans: Collection<int, Kegiatan>,
     *     eventKegiatans: Collection<int, Kegiatan>,
     *     rows: Collection<int, object>,
     *     pelatih: Pelatih|null,
     *     presensiPelatih: array<int, string>,
     *     totalHadir: int,
     *     totalIzin: int,
     *     totalSakit: int,
     *     totalAlpha: int,
     *     availableMonths: Collection<int, string>,
     * }
     */
    public function rekap(Ekskul $ekskul, ?string $bulan = null): array
    {
        $bulan = $this->normalizeBulan($bulan);

        $tanggalMulai = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth()->toDateString();
        $tanggalAkhir = Carbon::createFromFormat('Y-m', $bulan)->endOfMonth()->toDateString();

        $semuaKegiatan = $ekskul->kegiatans()
            ->whereBetween('tanggal_kegiatan', [$tanggalMulai, $tanggalAkhir])
            ->get();

        $kegiatans = $semuaKegiatan->reject(fn (Kegiatan $k) => $k->isEvent())->sortBy('tanggal_kegiatan')->values();
        $eventKegiatans = $semuaKegiatan->filter(fn (Kegiatan $k) => $k->isEvent())->sortBy('tanggal_kegiatan')->values();

        $pendaftarans = $ekskul->pendaftarans()
            ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
            ->with(['siswa.kelas'])
            ->get()
            ->sortBy(fn (Pendaftaran $p) => $p->siswa?->nama ?? '')
            ->values();

        $kegiatanIds = $semuaKegiatan->modelKeys();
        $pendaftaranIds = $pendaftarans->modelKeys();

        $presensis = Presensi::whereIn('pendaftaran_id', $pendaftaranIds)
            ->whereIn('kegiatan_id', $kegiatanIds)
            ->get()
            ->keyBy(fn (Presensi $p) => $p->pendaftaran_id.'-'.$p->kegiatan_id);

        $pelatih = $ekskul->pelatih;
        $presensiPelatih = $pelatih
            ? PresensiPelatih::where('pelatih_id', $pelatih->id)
                ->whereIn('kegiatan_id', $kegiatanIds)
                ->pluck('status', 'kegiatan_id')
                ->toArray()
            : [];

        $rows = $pendaftarans->map(function (Pendaftaran $pendaftaran) use ($semuaKegiatan, $kegiatans, $presensis) {
            $perKegiatan = [];
            $hadir = 0;
            $izin = 0;
            $sakit = 0;
            $alpha = 0;

            foreach ($semuaKegiatan as $kegiatan) {
                $status = $presensis->get($pendaftaran->id.'-'.$kegiatan->id)?->status;
                $perKegiatan[$kegiatan->id] = $status;

                if ($kegiatan->isEvent()) {
                    continue;
                }

                match ($status) {
                    Presensi::STATUS_HADIR => $hadir++,
                    Presensi::STATUS_IZIN => $izin++,
                    Presensi::STATUS_SAKIT => $sakit++,
                    Presensi::STATUS_ALPHA => $alpha++,
                    default => null,
                };
            }

            $total = $hadir + $izin + $sakit + $alpha;

            return (object) [
                'pendaftaran' => $pendaftaran,
                'sel' => $perKegiatan,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpha' => $alpha,
                'total' => $total,
                'persentaseKehadiran' => $total > 0 ? round(($hadir / $total) * 100, 1) : 0,
            ];
        });

        return [
            'ekskul' => $ekskul,
            'bulan' => $bulan,
            'kegiatans' => $kegiatans,
            'eventKegiatans' => $eventKegiatans,
            'rows' => $rows,
            'pelatih' => $pelatih,
            'presensiPelatih' => $presensiPelatih,
            'totalHadir' => $rows->sum('hadir'),
            'totalIzin' => $rows->sum('izin'),
            'totalSakit' => $rows->sum('sakit'),
            'totalAlpha' => $rows->sum('alpha'),
            'availableMonths' => $this->availableMonths($ekskul),
        ];
    }

    /**
     * Daftar bulan Y-m yang memiliki data presensi di ekskul tersebut.
     * Dipakai seragam oleh ketua, pembina, dan siswa.
     */
    protected function availableMonths(Ekskul $ekskul): Collection
    {
        $bulanTersedia = Presensi::whereHas('pendaftaran', function ($query) use ($ekskul) {
            $query->where('ekskul_id', $ekskul->id);
        })
            ->whereHas('kegiatan')
            ->with('kegiatan')
            ->get()
            ->map(fn (Presensi $p) => $p->kegiatan->tanggal_kegiatan->format('Y-m'))
            ->unique()
            ->sortDesc()
            ->values();

        return $bulanTersedia->isEmpty() ? collect([now()->format('Y-m')]) : $bulanTersedia;
    }
}