<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanBulanan extends Model
{
    protected $fillable = ['ekskul_id', 'bulan', 'materi_kegiatan', 'ringkasan', 'dokumentasi', 'dokumentasi_kegiatan', 'status', 'catatan_pembina', 'file_laporan', 'tujuan', 'kehadiran', 'evaluasi_keberhasilan', 'evaluasi_kendala', 'evaluasi_solusi'];

    protected $casts = [
        'dokumentasi_kegiatan' => 'array',
    ];

    public function ekskul(): BelongsTo
    {
        return $this->belongsTo(Ekskul::class);
    }
}
