<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pendaftaran extends Model
{
    protected $fillable = [
        'siswa_id',
        'ekskul_id',
        'tanggal_daftar',
        'status',
        // HAPUS 'alasan'
    ];

    protected $casts = [
        'tanggal_daftar' => 'date',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function ekskul(): BelongsTo
    {
        return $this->belongsTo(Ekskul::class);
    }

    public function presensis(): HasMany
    {
        return $this->hasMany(Presensi::class);
    }
}