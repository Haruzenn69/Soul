<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pembina extends Model
{
    protected $fillable = ['nip', 'user_id', 'nama', 'jenis_kelamin'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ekskuls(): HasMany
    {
        return $this->hasMany(Ekskul::class);
    }
}
