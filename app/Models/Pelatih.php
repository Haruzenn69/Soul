<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelatih extends Model
{
    public const VERIFIKASI_PENDING = 'pending';
    public const VERIFIKASI_TERVERIFIKASI = 'terverifikasi';
    public const VERIFIKASI_DITOLAK = 'ditolak';

    protected $fillable = [
        'pembina_id',
        'ekskul_id',
        'nama',
        'jenis_kelamin',
        'no_hp',
        'email',
        'sosmed',
        'alamat',
        'domisili',
        'cv',
        'sertifikat',
        'status',
        'status_verifikasi',
        'catatan_verifikasi',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function pembina(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Pembina::class);
    }

    public function ekskul(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Ekskul::class);
    }

    public function verifiedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function ekskuls(): HasMany
    {
        return $this->hasMany(Ekskul::class);
    }

    public function presensiPelatihs(): HasMany
    {
        return $this->hasMany(PresensiPelatih::class);
    }

    public function isTerverifikasi(): bool
    {
        return $this->status_verifikasi === self::VERIFIKASI_TERVERIFIKASI;
    }

    public function isPending(): bool
    {
        return $this->status_verifikasi === self::VERIFIKASI_PENDING;
    }

    public function isDitolak(): bool
    {
        return $this->status_verifikasi === self::VERIFIKASI_DITOLAK;
    }
}
