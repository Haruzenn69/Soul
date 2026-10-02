<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembinaProfileHistory extends Model
{
    protected $fillable = [
        'pembina_id',
        'changed_by_user_id',
        'field',
        'old_value',
        'new_value',
    ];

    public function pembina(): BelongsTo
    {
        return $this->belongsTo(Pembina::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
