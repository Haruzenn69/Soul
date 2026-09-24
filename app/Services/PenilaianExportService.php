<?php

namespace App\Services;

use App\Models\Ekskul;
use App\Models\Pendaftaran;
use App\Models\Penilaian;
use Illuminate\Support\Collection;

/**
 * Sumber data laporan penilaian akhir (PDF & Excel) agar angka
 * yang diunduh pembina dan kesiswaan selalu identik.
 */
class PenilaianExportService
{
    /**
     * Bangun baris laporan untuk satu ekskul.
     *
     * @param  string|null  $status  hanya row dengan penilaian berstatus ini.
     * @param  string|null  $periode  label periode (menggunakan periode berjalan bila kosong).
     * @return Collection<int, object>
     */
    public function rows(Ekskul $ekskul, ?string $status = null, ?string $periode = null): Collection
    {
        $periode = $periode ?: Penilaian::periodeSekarang()['label'];

        $pendaftarans = $ekskul->pendaftarans()
            ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
            ->with(['siswa.kelas'])
            ->get()
            ->sortBy(fn (Pendaftaran $p) => $p->siswa?->nama ?? '')
            ->values();

        $penilaians = Penilaian::where('periode', $periode)
            ->whereIn('pendaftaran_id', $pendaftarans->modelKeys())
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('updated_at')
            ->get()
            ->keyBy('pendaftaran_id');

        $periodeInfo = Penilaian::periodeSekarang();

        return $pendaftarans->map(function (Pendaftaran $p) use ($penilaians, $periodeInfo) {
            $penilaian = $penilaians->get($p->id);

            if ($penilaian) {
                $kehadiran = [
                    'total_pertemuan' => (int) $penilaian->total_pertemuan,
                    'total_hadir' => (int) $penilaian->total_hadir,
                    'total_izin' => (int) $penilaian->total_izin,
                    'total_sakit' => (int) $penilaian->total_sakit,
                    'total_alpha' => (int) $penilaian->total_alpha,
                    'persentase_kehadiran' => (float) $penilaian->persentase_kehadiran,
                ];
            } else {
                $kehadiran = Penilaian::kehadiran($p, $periodeInfo);
            }

            return (object) [
                'pendaftaran' => $p,
                'siswa' => $p->siswa,
                'kelas' => $p->siswa?->kelas?->nama ?? '-',
                'penilaian' => $penilaian,
                'total_pertemuan' => $kehadiran['total_pertemuan'],
                'total_hadir' => $kehadiran['total_hadir'],
                'total_izin' => $kehadiran['total_izin'],
                'total_sakit' => $kehadiran['total_sakit'],
                'total_alpha' => $kehadiran['total_alpha'],
                'persentase_kehadiran' => round($kehadiran['persentase_kehadiran'], 2),
                'nilai_sikap' => $penilaian?->nilai_sikap ?? 0,
                'nilai_keaktifan' => $penilaian?->nilai_keaktifan ?? 0,
                'nilai_keterampilan' => $penilaian?->nilai_keterampilan ?? 0,
                'nilai_akhir' => $penilaian?->nilai_akhir ?? 0,
                'predikat' => $penilaian?->predikat ?? '-',
                'catatan' => $penilaian?->catatan ?? '-',
                'status' => $penilaian?->status ?? '-',
            ];
        });
    }

    /**
     * Data ringkas untuk halaman detail (kesiswaan & siswa).
     */
    public function summary(Ekskul $ekskul): array
    {
        $rows = $this->rows($ekskul, Penilaian::STATUS_TERKIRIM);

        return [
            'totalAnggota' => $rows->count(),
            'sudahDinilai' => $rows->where('penilaian', '!=', null)->count(),
            'rataAkhir' => $rows->where('nilai_akhir', '>', 0)->avg('nilai_akhir') ?? 0,
            'tertinggi' => $rows->where('nilai_akhir', '>', 0)->max('nilai_akhir') ?? 0,
        ];
    }
}
