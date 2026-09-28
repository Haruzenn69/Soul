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

    public function notifikasis(): HasMany
    {
        return $this->hasMany(Notifikasi::class);
    }

    public function pengajuanKeluar(): HasMany
    {
        return $this->pengajuanKeluars();
    }

    public function activePendaftaran(): ?Pendaftaran
    {
        return $this->pendaftarans()
            ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
            ->latest('id')
            ->first();
    }

    public function activeEkskul(): ?Ekskul
    {
        return $this->activePendaftaran()?->ekskul;
    }

    public function pendingPendaftaran(): ?Pendaftaran
    {
        return $this->pendaftarans()
            ->where('status', Pendaftaran::STATUS_PENDING)
            ->latest('id')
            ->first();
    }

    public function isKetua(): bool
    {
        return $this->jabatan === 'ketua';
    }

    public function isProfileComplete(): bool
    {
        return filled($this->nama) && filled($this->kelas_id) && filled($this->jenis_kelamin);
    }
}
