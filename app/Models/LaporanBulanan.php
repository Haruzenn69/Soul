<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanBulanan extends Model
{
    protected $fillable = ['ekskul_id', 'bulan', 'materi_kegiatan', 'ringkasan', 'dokumentasi', 'status', 'catatan_pembina', 'file_laporan'];

    public function ekskul(): BelongsTo
    {
        return $this->belongsTo(Ekskul::class);
    }
}
