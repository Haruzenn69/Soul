<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAjaran extends Model
{
    protected $fillable = [
        'nama',
        'status',
    ];

    // ────────────────────────────────────
    // Backward compat: is_active accessor
    // ────────────────────────────────────
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'aktif';
    }

    // ────────────────────────────────────
    // Scopes
    // ────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeHistoris($query)
    {
        return $query->where('status', 'historis');
    }

    public function scopeNonaktif($query)
    {
        return $query->where('status', 'nonaktif');
    }

    // ────────────────────────────────────
    // Relations
    // ────────────────────────────────────
    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class);
    }

    // ────────────────────────────────────
    // Helpers
    // ────────────────────────────────────
    public static function getActive(): ?self
    {
        return static::active()->first();
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    public function isHistoris(): bool
    {
        return $this->status === 'historis';
    }

    public function isNonaktif(): bool
    {
        return $this->status === 'nonaktif';
    }
}
