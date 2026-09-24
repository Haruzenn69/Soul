<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Faq extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_ANSWERED = 'answered';

    protected $fillable = ['ekskul_id', 'user_id', 'pertanyaan', 'jawaban', 'status'];

    protected $casts = [
        'status' => 'string',
    ];

    public function ekskul(): BelongsTo
    {
        return $this->belongsTo(Ekskul::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}