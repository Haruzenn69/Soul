<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiswaRiwayatKelas extends Model
{
    protected $table = 'siswa_riwayat_kelas';

    protected $fillable = [
        'siswa_id',
        'tahun_ajaran',
        'kelas_asal',
        'kelas_tujuan',
        'jenis',
        'diproses_pada',
    ];

    protected function casts(): array
    {
        return ['diproses_pada' => 'datetime'];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }
}
