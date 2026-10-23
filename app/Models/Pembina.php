<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pembina extends Model
{
    protected $fillable = [
        'user_id',
        'nip',
        'foto',
        'kategori_pernah_dibina',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'jenis_kelamin',
        'email',
        'no_telp',
        'alamat',
        'medsos',
    ];

    public function getFotoUrlAttribute(): string
    {
        if ($this->foto) {
            if (str_starts_with($this->foto, 'http://') || str_starts_with($this->foto, 'https://')) {
                return $this->foto;
            }
            if (str_starts_with($this->foto, 'images/')) {
                return asset($this->foto);
            }
            return asset('storage/'.$this->foto);
        }

        return asset('images/default.png');
    }

    protected function nama(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value !== null ? mb_strtoupper(trim($value)) : null,
        );
    }

    /**
     * Semua kategori unik dari ekskul yang saat ini aktif dibina.
     * Mendukung pembinaan lintas kategori (misal: "Olahraga, Seni").
     */
    public function getKategoriAktifAttribute(): ?string
    {
        $kategoriList = $this->ekskuls
            ->pluck('kategori')
            ->filter()
            ->unique()
            ->values();

        return $kategoriList->isNotEmpty() ? $kategoriList->join(', ') : null;
    }

    /**
     * Kategori yang terafiliasi dengan pembina: kategori aktif jika sedang membina, atau riwayat pernah membina.
     */
    public function getKategoriBinaanAttribute(): ?string
    {
        return $this->kategori_aktif ?: $this->kategori_pernah_dibina;
    }

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ekskuls(): HasMany
    {
        return $this->hasMany(Ekskul::class);
    }

    public function profileHistories(): HasMany
    {
        return $this->hasMany(PembinaProfileHistory::class)->latest();
    }

    public function notifikasis(): HasMany
    {
        return $this->hasMany(Notifikasi::class);
    }

    public function pelatihs(): HasMany
    {
        return $this->hasMany(Pelatih::class);
    }

    public function isProfileComplete(): bool
    {
        return filled($this->nama) && filled($this->jenis_kelamin);
    }
}
