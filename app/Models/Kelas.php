<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $fillable = [
        'nama',
        'tingkat',
        'jurusan',
        'rombel',
        'tahun_ajaran_id',
    ];

    public function getJurusanLabelAttribute(): ?string
    {
        return $this->jurusan
            ? config("kelas.jurusan.{$this->jurusan}.{$this->tingkat}")
            : null;
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function siswas(): HasMany
    {
        return $this->hasMany(Siswa::class);
    }
}
