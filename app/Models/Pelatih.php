<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelatih extends Model
{
    protected $fillable = ['nama', 'jenis_kelamin', 'no_hp', 'status'];

    protected $casts = [
        'status' => 'string',
    ];
}
