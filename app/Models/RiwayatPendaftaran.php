<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPendaftaran extends Model
{
    protected $fillable = [
        'pendaftaran_id',
        'siswa_id',
        'ekskul_id',
        'tanggal_daftar',
        'status',
        'alasan',
        'dicatat_pada',
    ];

    protected $casts = [
        'tanggal_daftar' => 'date',
        'dicatat_pada' => 'datetime',
    ];

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function ekskul(): BelongsTo
    {
        return $this->belongsTo(Ekskul::class);
    }
}
