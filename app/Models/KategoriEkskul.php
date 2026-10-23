<?php

namespace App\Models;

use App\Support\AturanEkskul;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriEkskul extends Model
{
    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    public function ekskuls(): HasMany
    {
        return $this->hasMany(Ekskul::class, 'kategori', 'nama');
    }

    public function isWajib(): bool
    {
        return AturanEkskul::isWajib($this->nama);
    }

    /** Kategori fallback sistem (Lainnya) — tidak bisa dihapus. */
    public function isFallback(): bool
    {
        return AturanEkskul::isFallback($this->nama);
    }

    /** Kategori sistem (Wajib atau Lainnya) — tidak bisa dihapus. */
    public function isSistem(): bool
    {
        return AturanEkskul::isSistem($this->nama);
    }
}
