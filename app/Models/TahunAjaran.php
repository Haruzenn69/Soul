<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAjaran extends Model
{
    protected $fillable = [
        "nama",
        "status",
    ];

    public function getIsActiveAttribute(): bool
    {
        return $this->status === "aktif";
    }

    public function scopeActive($query)
    {
        return $query->where("status", "aktif");
    }

    public function scopeHistoris($query)
    {
        return $query->where("status", "historis");
    }

    public function scopeNonaktif($query)
    {
        return $query->where("status", "nonaktif");
    }

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class);
    }

    public static function getActive(): ?self
    {
        return static::where("status", "aktif")->first();
    }

    public function isAktif(): bool
    {
        return $this->status === "aktif";
    }

    public function isActive(): bool
    {
        return $this->status === "aktif";
    }

    public function isHistoris(): bool
    {
        return $this->status === "historis";
    }

    public function isNonaktif(): bool
    {
        return $this->status === "nonaktif";
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            "aktif" => "Aktif",
            "historis" => "Historis",
            "nonaktif" => "Nonaktif",
            default => ucfirst($this->status ?? "unknown"),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            "aktif" => "bg-green-100 text-green-800",
            "historis" => "bg-blue-100 text-blue-800",
            "nonaktif" => "bg-gray-100 text-gray-800",
            default => "bg-gray-100 text-gray-800",
        };
    }
}
