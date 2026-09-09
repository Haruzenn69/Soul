<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SiswaEkskulSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Pembina (Tanpa field 'name')
        User::firstOrCreate([
            'email' => 'pembina@soul.test',
        ], [
            'username' => 'pembina',
            'password' => Hash::make('password123'),
            'role'     => 'pembina',
        ]);

        // 2. Akun Siswa Nazwa (Tanpa field 'name')
        User::firstOrCreate([
            'email' => 'nazwa@gmail.com',
        ], [
            'username' => 'nazwanurhafiza',
            'password' => Hash::make('password123'),
            'role'     => 'siswa',
        ]);
    }
}