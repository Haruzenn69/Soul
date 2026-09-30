<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kegiatan extends Model
{
    protected $fillable = ['ekskul_id', 'materi', 'jenis_kegiatan', 'deskripsi', 'dokumentasi', 'tanggal_kegiatan', 'tanggal_berakhir'];

    protected $casts = [
        'tanggal_kegiatan' => 'date',
        'tanggal_berakhir' => 'date',
    ];

    public const JENIS_EVENT = 'event';

    public function isEvent(): bool
    {
        return $this->jenis_kegiatan === self::JENIS_EVENT;
    }

    public function tanggalText(): string
    {
        if ($this->isEvent() && $this->tanggal_berakhir && $this->tanggal_berakhir->notEqualTo($this->tanggal_kegiatan)) {
            return $this->tanggal_kegiatan->translatedFormat('d F Y').' - '.$this->tanggal_berakhir->translatedFormat('d F Y');
        }

        return $this->tanggal_kegiatan->translatedFormat('d F Y');
    }

    public function ekskul(): BelongsTo
    {
        return $this->belongsTo(Ekskul::class);
    }

    public function presensis(): HasMany
    {
        return $this->hasMany(Presensi::class);
    }

    public function presensiPelatihs(): HasMany
    {
        return $this->hasMany(PresensiPelatih::class);
    }
}
