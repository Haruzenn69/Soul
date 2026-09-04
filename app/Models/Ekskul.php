<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ekskul extends Model
{
    protected $fillable = [
        'pembina_id',
        'pelatih_id',
        'nama_ekskul',
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
}
