<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanKeluar extends Model
{
    protected $fillable = ['siswa_id', 'ekskul_id', 'alasan', 'status', 'tanggal_pengajuan'];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function ekskul(): BelongsTo
    {
        return $this->belongsTo(Ekskul::class);
    }
}
