<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ekskul extends Model
{
    protected $fillable = ['pembina_id', 'pelatih_id', 'nama_ekskul', 'deskripsi', 'jadwal', 'is_open_recruitment'];

    protected $casts = [
        'is_open_recruitment' => 'boolean',
    ];

    public function pembina(): BelongsTo
    {
        return $this->belongsTo(Pembina::class);
    }

    public function pelatih(): BelongsTo
    {
        return $this->belongsTo(Pelatih::class);
    }
}
