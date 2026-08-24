<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Pelatih;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Tahun Ajaran aktif (dibutuhkan untuk CRUD kelas)
        $tahunAjaran = TahunAjaran::create([
            'nama' => '2026/2027',
            'is_active' => true,
        ]);

        // Kelas contoh
        foreach ([['X RPL 1', 'x'], ['XI RPL 1', 'xi'], ['XI RPL 2', 'xi'], ['XII RPL 1', 'xii']] as [$nama, $tingkat]) {
            Kelas::create([
                'nama' => $nama,
                'tingkat' => $tingkat,
                'tahun_ajaran_id' => $tahunAjaran->id,
            ]);
        }

        // Pelatih contoh (dibutuhkan untuk CRUD ekskul)
        Pelatih::create([
            'nama' => 'Pelatih Contoh',
            'jenis_kelamin' => 'laki-laki',
            'no_hp' => '081234567890',
            'status' => 'aktif',
        ]);

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

        // Akun Kesiswaan (login: kesiswaan@soul.test / password)
        User::factory()->create([
            'username' => 'kesiswaan',
            'email' => 'kesiswaan@soul.test',
            'role' => 'kesiswaan',
        ]);
    }
}