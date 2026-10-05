<?php

namespace App\Models;

use App\Support\EkskulInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ekskul extends Model
{
    protected $fillable = [
        'pembina_id',
        'pelatih_id',
        'nama_ekskul',
        'kategori',
        'deskripsi',
        'tagline',
        'tujuan',
        'logo',
        'cover',
        'jadwal',
        'is_open_recruitment',
        'status',
    ];

    protected $casts = [
        'is_open_recruitment' => 'boolean',
        'status' => 'boolean',
    ];

    public function pembina(): BelongsTo
    {
        return $this->belongsTo(Pembina::class);
    }

    public function pelatih(): BelongsTo
    {
        return $this->belongsTo(Pelatih::class);
    }

    public function pendaftarans(): HasMany
    {
        return $this->hasMany(Pendaftaran::class);
    }

    public function kegiatans(): HasMany
    {
        return $this->hasMany(Kegiatan::class)->orderBy('tanggal_kegiatan');
    }

    public function pengajuanKeluars(): HasMany
    {
        return $this->hasMany(PengajuanKeluar::class);
    }

    public function laporanBulanans(): HasMany
    {
        return $this->hasMany(LaporanBulanan::class);
    }

    public function prestasis(): HasMany
    {
        return $this->hasMany(Prestasi::class);
    }

    public function testimoniss(): HasMany
    {
        return $this->hasMany(Testimoni::class);
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class);
    }

    public function galeris(): HasMany
    {
        return $this->hasMany(EkskulGaleri::class)->latest();
    }

    public function riwayatJabatans(): HasMany
    {
        return $this->hasMany(RiwayatJabatan::class);
    }

    public function riwayatKetua(): HasMany
    {
        return $this->riwayatJabatans()->where('jabatan', RiwayatJabatan::JABATAN_KETUA);
    }

    /**
     * Periode ketua yang masih berjalan, bila ada.
     */
    public function periodeKetuaAktif(): ?RiwayatJabatan
    {
        return $this->riwayatKetua()
            ->whereNull('selesai')
            ->orderByDesc('mulai')
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Semua periode ketua yang sudah selesai (data arsip).
     */
    public function periodeKetuaSelesai(): HasMany
    {
        return $this->riwayatKetua()
            ->whereNotNull('selesai')
            ->orderByDesc('selesai');
    }

    public function ketua(): ?Siswa
    {
        return $this->pendaftarans()
            ->where('status', Pendaftaran::STATUS_DITERIMA)
            ->whereHas('siswa', fn ($q) => $q->where('jabatan', 'ketua'))
            ->first()
            ?->siswa;
    }

    public function getBidangAttribute(): string
    {
        return EkskulInfo::bidang($this->nama_ekskul);
    }
}
