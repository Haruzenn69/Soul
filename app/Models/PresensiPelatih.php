<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresensiPelatih extends Model
{
    public const STATUS_HADIR = 'hadir';

    public const STATUS_IZIN = 'izin';

    public const STATUS_SAKIT = 'sakit';

    public const STATUS_ALPHA = 'alpha';

    protected $table = 'presensi_pelatihs';

    protected $fillable = [
        'kegiatan_id',
        'pelatih_id',
        'status',
    ];

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function pelatih(): BelongsTo
    {
        return $this->belongsTo(Pelatih::class);
    }
}