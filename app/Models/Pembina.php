<?php

namespace App\Models;

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

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? asset('storage/'.$this->foto) : null;
    }

    /**
     * Kategori ekskul yang saat ini aktif dibina (jika membina ekskul).
     */
    public function getKategoriAktifAttribute(): ?string
    {
        return $this->ekskuls->first()?->kategori;
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
