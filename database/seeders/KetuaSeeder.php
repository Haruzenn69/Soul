<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Pelatih;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Models\Pembina;
use App\Models\Ekskul;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KetuaSeeder extends Seeder
{
    public function run(): void
    {
        // Tahun Ajaran + Kelas
        $tahunAjaran = TahunAjaran::firstOrCreate(
            ['nama' => '2026/2027'],
            ['is_active' => true]
        );

        $kelas = Kelas::firstOrCreate(
            ['nama' => 'XI RPL 1', 'tahun_ajaran_id' => $tahunAjaran->id],
            ['tingkat' => 'xi']
        );

        // User + Siswa (Ketua)
        $userKetua = User::create([
            'username' => 'ketua01',
            'email'    => 'ketua01@soul.test',
            'password' => Hash::make('password'),
            'role'     => 'siswa',
        ]);

        $userKetua->siswa()->create([
            'nis'           => '2406510001',
            'nama'          => 'Ketua Test',
            'kelas_id'      => $kelas->id,
            'jenis_kelamin' => 'laki-laki',
            'jabatan'       => 'ketua',
        ]);

        // User + Pembina
        $userPembina = User::create([
            'username' => 'pembina01',
            'email'    => 'pembina01@soul.test',
            'password' => Hash::make('password'),
            'role'     => 'pembina',
        ]);

        $pembina = $userPembina->pembina()->create([
            'nip'             => '198501012020011001',
            'nama'            => 'Pembina Test',
            'jenis_kelamin'   => 'laki-laki',
        ]);

        // Pelatih
        $pelatih = Pelatih::create([
            'nama'          => 'Pelatih Karate',
            'jenis_kelamin' => 'laki-laki',
            'no_hp'         => '081298765432',
            'status'        => 'aktif',
        ]);

        // Ekskul
        Ekskul::create([
            'pembina_id'          => $pembina->id,
            'pelatih_id'          => $pelatih->id,
            'nama_ekskul'         => 'Karate',
            'deskripsi'           => 'Ekstrakurikuler bela diri karate',
            'jadwal'              => 'Senin & Rabu, 15:30 - 17:00',
            'is_open_recruitment' => true,
        ]);
    }
}
