<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prestasi extends Model
{
    protected $fillable = ['ekskul_id', 'judul', 'kategori', 'tahun', 'foto'];

    public function ekskul(): BelongsTo
    {
        return $this->belongsTo(Ekskul::class);
    }
}