<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengurus extends Model
{
    protected $table = 'pengurus';

    public const JABATAN_KETUA = 'ketua';
    public const JABATAN_WAKIL_KETUA = 'wakil_ketua';
    public const JABATAN_SEKRETARIS = 'sekretaris';
    public const JABATAN_BENDAHARA = 'bendahara';

    public const STATUS_AKTIF = 'aktif';
    public const STATUS_MENUNGGU = 'menunggu';
    public const STATUS_DITOLAK = 'ditolak';
    public const STATUS_SELESAI = 'selesai';

    protected $fillable = [
        'ekskul_id',
        'siswa_id',
        'jabatan',
        'status',
        'diusulkan_oleh',
        'catatan',
    ];

    public static function daftarJabatan(): array
    {
        return [
            self::JABATAN_KETUA => 'Ketua',
            self::JABATAN_WAKIL_KETUA => 'Wakil Ketua',
            self::JABATAN_SEKRETARIS => 'Sekretaris',
            self::JABATAN_BENDAHARA => 'Bendahara',
        ];
    }

    /**
     * Jabatan yang boleh diusulkan oleh ketua (wakil ketua, sekretaris, bendahara).
     * Ketua TIDAK boleh mengusulkan jabatan ketua.
     */
    public static function daftarJabatanUsulanKetua(): array
    {
        return [
            self::JABATAN_WAKIL_KETUA => 'Wakil Ketua',
            self::JABATAN_SEKRETARIS => 'Sekretaris',
            self::JABATAN_BENDAHARA => 'Bendahara',
        ];
    }

    public static function labelJabatan(?string $jabatan): string
    {
        return self::daftarJabatan()[$jabatan] ?? ucfirst(str_replace('_', ' ', (string) $jabatan));
    }

    public static function labelStatus(?string $status): string
    {
        return match ($status) {
            self::STATUS_AKTIF => 'Aktif',
            self::STATUS_MENUNGGU => 'Menunggu Persetujuan',
            self::STATUS_DITOLAK => 'Ditolak',
            self::STATUS_SELESAI => 'Selesai / Dicopot',
            default => ucfirst((string) $status),
        };
    }

    public function ekskul(): BelongsTo
    {
        return $this->belongsTo(Ekskul::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function isAktif(): bool
    {
        return $this->status === self::STATUS_AKTIF;
    }

    public function isMenunggu(): bool
    {
        return $this->status === self::STATUS_MENUNGGU;
    }

    public function isDitolak(): bool
    {
        return $this->status === self::STATUS_DITOLAK;
    }

    public function isSelesai(): bool
    {
        return $this->status === self::STATUS_SELESAI;
    }
}
