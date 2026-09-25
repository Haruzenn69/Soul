<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimoni extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = ['ekskul_id', 'user_id', 'nama', 'kelas', 'quote', 'status'];

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
