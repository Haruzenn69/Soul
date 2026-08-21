<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Siswa extends Model
{
    protected $fillable = ['nis', 'user_id', 'nama', 'jenis_kelamin', 'jabatan'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
