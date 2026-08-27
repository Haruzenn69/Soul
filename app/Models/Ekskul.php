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
        'jadwal',
        'is_open_recruitment',
        // HAPUS 'logo', 'visi', 'misi', 'status'
    ];

    protected $casts = [
        'is_open_recruitment' => 'boolean',
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
        return $this->hasMany(Kegiatan::class);
    }

    public function pengajuanKeluars(): HasMany
    {
        return $this->hasMany(PengajuanKeluar::class);
    }

    public function laporanBulanans(): HasMany
    {
        return $this->hasMany(LaporanBulanan::class);
    }
}
