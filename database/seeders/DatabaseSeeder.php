<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Akun Admin
        User::factory()->create([
            'username' => 'admin',
            'email'    => 'admin@soul.test',
            'role'     => 'admin',
        ]);

        // Panggil seeder akun siswa Nazwa
        $this->call([
            SiswaEkskulSeeder::class,
        ]);
    }
}