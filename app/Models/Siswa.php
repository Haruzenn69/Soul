<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    protected $fillable = [
        'user_id',
        'nis',
        'nama',              // ← GANTI dari 'nama_lengkap' ke 'nama'
        'kelas_id',
        'jenis_kelamin',
        'jabatan',
        // HAPUS 'no_telepon' dan 'alamat'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function pendaftarans(): HasMany
    {
        return $this->hasMany(Pendaftaran::class);
    }

    public function pengajuanKeluars(): HasMany
    {
        return $this->hasMany(PengajuanKeluar::class);
    }

    public function pengajuanKeluar(): HasMany
    {
        return $this->pengajuanKeluars();
    }
}
