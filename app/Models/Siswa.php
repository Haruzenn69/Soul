<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    protected $fillable = ['nis', 'user_id', 'nama', 'kelas_id', 'jenis_kelamin', 'jabatan'];

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

    // === TAMBAHKAN DUA METHOD DI BAWAH INI ===

    public function presensis(): HasMany
    {
        return $this->hasMany(Presensi::class);
    }

    public function ekskulAktif()
    {
        return $this->hasOneThrough(
            Ekskul::class,
            Pendaftaran::class,
            'siswa_id',    // Foreign key di tabel pendaftarans
            'id',          // Foreign key di tabel ekskuls
            'id',          // Local key di tabel siswas
            'ekskul_id'    // Local key di tabel pendaftarans
        )->where('pendaftarans.status', 'diterima');
    }
}
