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
     * Tentukan periode semester berjalan beserta rentang tanggalnya berdasarkan Tahun Ajaran aktif.
     *
     * @return array{label: string, semester: string, tahun_ajaran: string, start: string, end: string}
     */
    public static function periodeSekarang(?TahunAjaran $ta = null): array
    {
        $ta = $ta ?: TahunAjaran::getActive();
        $month = now()->month;

        if ($ta && preg_match('/^(\d{4})\s*\/\s*(\d{4})$/', trim($ta->nama), $matches)) {
            $tahunAwal = (int) $matches[1];
            $tahunAkhir = (int) $matches[2];
        } else {
            if ($month >= 7) {
                $tahunAwal = now()->year;
                $tahunAkhir = $tahunAwal + 1;
            } else {
                $tahunAkhir = now()->year;
                $tahunAwal = $tahunAkhir - 1;
            }
        }

        if ($month >= 7) {
            $semester = 'Ganjil';
            $start = "{$tahunAwal}-07-01";
            $end = "{$tahunAwal}-12-31";
        } else {
            $semester = 'Genap';
            $start = "{$tahunAkhir}-01-01";
            $end = "{$tahunAkhir}-06-30";
        }

        $taNama = "{$tahunAwal}/{$tahunAkhir}";
        $label = "{$semester} {$taNama}";

        return [
            'label' => $label,
            'semester' => $semester,
            'tahun_ajaran' => $taNama,
            'start' => $start,
            'end' => $end,
        ];
    }

    /**
     * Parse label periode (misal: "Ganjil 2029/2030") menjadi informasi detail dan rentang tanggal.
     *
     * @return array{label: string, semester: string, tahun_ajaran: string, start: string, end: string}
     */
    public static function parsePeriode(?string $periode): array
    {
        if (! $periode) {
            return static::periodeSekarang();
        }

        if (preg_match('/^(Ganjil|Genap)\s+(\d{4})\s*\/\s*(\d{4})$/i', trim($periode), $matches)) {
            $semester = ucfirst(strtolower($matches[1]));
            $tahunAwal = (int) $matches[2];
            $tahunAkhir = (int) $matches[3];
            $taNama = "{$tahunAwal}/{$tahunAkhir}";

            if ($semester === 'Ganjil') {
                $start = "{$tahunAwal}-07-01";
                $end = "{$tahunAwal}-12-31";
            } else {
                $start = "{$tahunAkhir}-01-01";
                $end = "{$tahunAkhir}-06-30";
            }

            return [
                'label' => "{$semester} {$taNama}",
                'semester' => $semester,
                'tahun_ajaran' => $taNama,
                'start' => $start,
                'end' => $end,
            ];
        }

        return static::periodeSekarang();
    }

    /**
     * Dapatkan daftar periode yang tersedia (dari TahunAjaran aktif, historis, dan record DB).
     *
     * @return array<int, array{label: string, semester: string, tahun_ajaran: string, is_aktif: bool, is_historis: bool}>
     */
    public static function daftarPeriode(): array
    {
        $aktifTa = TahunAjaran::getActive();
        $periodeAktif = static::periodeSekarang($aktifTa)['label'];

        $daftar = [];
        $tahuns = TahunAjaran::orderBy('id', 'desc')->get();

        foreach ($tahuns as $ta) {
            $nama = trim($ta->nama);
            foreach (['Genap', 'Ganjil'] as $sem) {
                $lbl = "{$sem} {$nama}";
                $daftar[$lbl] = [
                    'label' => $lbl,
                    'semester' => $sem,
                    'tahun_ajaran' => $nama,
                    'is_aktif' => ($lbl === $periodeAktif),
                    'is_historis' => ($ta->status === 'historis'),
                ];
            }
        }

        // Tambahkan juga periode yang tercatat di tabel penilaian jika ada yang belum tercakup
        $dbPeriodes = static::query()->distinct()->pluck('periode')->filter();
        foreach ($dbPeriodes as $lbl) {
            if (! isset($daftar[$lbl])) {
                $info = static::parsePeriode($lbl);
                $daftar[$lbl] = [
                    'label' => $lbl,
                    'semester' => $info['semester'],
                    'tahun_ajaran' => $info['tahun_ajaran'],
                    'is_aktif' => ($lbl === $periodeAktif),
                    'is_historis' => true,
                ];
            }
        }

        // Urutkan dengan menaruh periode aktif di paling atas
        uasort($daftar, function ($a, $b) use ($periodeAktif) {
            if ($a['label'] === $periodeAktif) {
                return -1;
            }
            if ($b['label'] === $periodeAktif) {
                return 1;
            }
            return strcmp($b['label'], $a['label']);
        });

        return array_values($daftar);
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
