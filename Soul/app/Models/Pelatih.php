<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelatih extends Model
{
    protected $fillable = [
        'nama',
        'jenis_kelamin',
        'no_hp',
        'status'
    ];

    public function ekskuls(): HasMany
    {
        return $this->hasMany(Ekskul::class);
    }
}