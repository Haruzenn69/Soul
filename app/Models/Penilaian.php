<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penilaian extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_TERKIRIM = 'terkirim';

    protected $fillable = [
        'pendaftaran_id',
        'periode',
        'total_pertemuan',
        'total_hadir',
        'total_izin',
        'total_sakit',
        'total_alpha',
        'persentase_kehadiran',
        'nilai_sikap',
        'nilai_keaktifan',
        'nilai_keterampilan',
        'nilai_akhir',
        'predikat',
        'catatan',
        'status',
        'dikirim_at',
        'dinilai_oleh',
    ];

    protected $casts = [
        'total_pertemuan' => 'integer',
        'total_hadir' => 'integer',
        'total_izin' => 'integer',
        'total_sakit' => 'integer',
        'total_alpha' => 'integer',
        'persentase_kehadiran' => 'float',
        'nilai_sikap' => 'float',
        'nilai_keaktifan' => 'float',
        'nilai_keterampilan' => 'float',
        'nilai_akhir' => 'float',
        'dikirim_at' => 'datetime',
    ];

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function penilai(): BelongsTo
    {
        return $this->belongsTo(Pembina::class, 'dinilai_oleh');
    }

    public function isTerkirim(): bool
    {
        return $this->status === self::STATUS_TERKIRIM;
    }

    /**
     * Nilai Akhir = (Persentase Kehadiran × 30%) + (Sikap × 25%)
     *              + (Keaktifan × 25%) + (Keterampilan × 20%)
     */
    public static function hitungNilaiAkhir(
        float|int $persentaseKehadiran,
        float|int $nilaiSikap,
        float|int $nilaiKeaktifan,
        float|int $nilaiKeterampilan
    ): float {
        return round(
            ($persentaseKehadiran * 0.30)
            + ($nilaiSikap * 0.25)
            + ($nilaiKeaktifan * 0.25)
            + ($nilaiKeterampilan * 0.20),
            2
        );
    }

    /**
     * Predikat: A (90-100), B (80-89), C (70-79), D (60-69), E (<60).
     */
    public static function hitungPredikat(float|int $nilaiAkhir): string
    {
        return match (true) {
            $nilaiAkhir >= 90 => 'A',
            $nilaiAkhir >= 80 => 'B',
            $nilaiAkhir >= 70 => 'C',
            $nilaiAkhir >= 60 => 'D',
            default => 'E',
        };
    }

    public static function warnaPredikat(string $predikat): string
    {
        return match ($predikat) {
            'A' => 'text-emerald-600 bg-emerald-50 border-emerald-200',
            'B' => 'text-sky-600 bg-sky-50 border-sky-200',
            'C' => 'text-amber-600 bg-amber-50 border-amber-200',
            'D' => 'text-orange-600 bg-orange-50 border-orange-200',
            default => 'text-red-600 bg-red-50 border-red-200',
        };
    }

    /**
     * Tentukan periode semester berjalan beserta rentang tanggalnya.
     *
     * @return array{label: string, start: string, end: string}
     */
    public static function periodeSekarang(): array
    {
        $month = now()->month;

        if ($month >= 7) {
            $tahun = now()->year;
            $label = "Ganjil {$tahun}/".($tahun + 1);
            $start = "{$tahun}-07-01";
            $end = "{$tahun}-12-31";
        } else {
            $tahunAkhir = now()->year;
            $tahunAwal = $tahunAkhir - 1;
            $label = "Genap {$tahunAwal}/{$tahunAkhir}";
            $start = "{$tahunAkhir}-01-01";
            $end = "{$tahunAkhir}-06-30";
        }

        return [
            'label' => $label,
            'start' => $start,
            'end' => $end,
        ];
    }

    /**
     * Hitung kehadiran anggota pada rentang periode sebuah ekskul.
     *
     * @return array{total_pertemuan: int, total_hadir: int, total_izin: int, total_sakit: int, total_alpha: int, persentase_kehadiran: float}
     */
    public static function kehadiran(Pendaftaran $pendaftaran, array $periode): array
    {
        $kegiatanIds = Kegiatan::where('ekskul_id', $pendaftaran->ekskul_id)
            ->whereBetween('tanggal_kegiatan', [$periode['start'], $periode['end']])
            ->pluck('id');

        $presensis = Presensi::where('pendaftaran_id', $pendaftaran->id)
            ->whereIn('kegiatan_id', $kegiatanIds)
            ->pluck('status');

        $hadir = $presensis->filter(fn ($s) => $s === Presensi::STATUS_HADIR)->count();
        $izin = $presensis->filter(fn ($s) => $s === Presensi::STATUS_IZIN)->count();
        $sakit = $presensis->filter(fn ($s) => $s === Presensi::STATUS_SAKIT)->count();
        $alpha = $presensis->filter(fn ($s) => $s === Presensi::STATUS_ALPHA)->count();

        $total = $presensis->count();

        return [
            'total_pertemuan' => $total,
            'total_hadir' => $hadir,
            'total_izin' => $izin,
            'total_sakit' => $sakit,
            'total_alpha' => $alpha,
            'persentase_kehadiran' => $total > 0 ? round(($hadir / $total) * 100, 2) : 0.0,
        ];
    }

    public static function periodeLabel(string $periode): string
    {
        return $periode;
    }

    public static function dikirimLabel(?Carbon $dikirimAt): ?string
    {
        return $dikirimAt?->translatedFormat('d M Y H:i');
    }
}
