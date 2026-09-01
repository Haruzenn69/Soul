<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Faq extends Model
{
    protected $fillable = ['ekskul_id', 'pertanyaan', 'jawaban'];

    public function ekskul(): BelongsTo
    {
        return $this->belongsTo(Ekskul::class);
    }
}