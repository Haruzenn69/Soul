<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kegiatan extends Model
{
    protected $fillable = [
        'ekskul_id',
        'materi',           // ← HANYA INI (sesuai migration)
        'tanggal_kegiatan', // ← HANYA INI (sesuai migration)
        // HAPUS 'nama_kegiatan', 'waktu_mulai', 'waktu_selesai', 'tempat', 'deskripsi'
    ];

    protected $casts = [
        'tanggal_kegiatan' => 'date',
    ];

    public function ekskul(): BelongsTo
    {
        return $this->belongsTo(Ekskul::class);
    }

    public function presensis(): HasMany
    {
        return $this->hasMany(Presensi::class);
    }
}