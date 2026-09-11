<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifikasi extends Model
{
    protected $fillable = [
        'siswa_id',
        'pembina_id',
        'pendaftaran_id',
        'pengajuan_keluar_id',
        'laporan_bulanan_id',
        'judul',
        'pesan',
        'tipe',
        'is_read',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function pembina(): BelongsTo
    {
        return $this->belongsTo(Pembina::class);
    }

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function pengajuanKeluar(): BelongsTo
    {
        return $this->belongsTo(PengajuanKeluar::class, 'pengajuan_keluar_id');
    }

    public function laporanBulanan(): BelongsTo
    {
        return $this->belongsTo(LaporanBulanan::class, 'laporan_bulanan_id');
    }
}