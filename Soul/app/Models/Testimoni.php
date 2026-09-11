<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimoni extends Model
{
    protected $fillable = ['ekskul_id', 'nama', 'kelas', 'quote'];

    public function ekskul(): BelongsTo
    {
        return $this->belongsTo(Ekskul::class);
    }
}