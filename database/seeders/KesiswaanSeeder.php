<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KesiswaanSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'kesiswaan@smkn11bdg.sch.id'],
            [
                'username' => 'kesiswaan',
                'password' => Hash::make('kesiswaan123'),
                'role' => 'kesiswaan',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@smkn11bdg.sch.id'],
            [
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );
    }
}