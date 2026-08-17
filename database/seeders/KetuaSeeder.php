<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Pembina;
use App\Models\Pelatih;
use App\Models\Ekskul;
use App\Models\Pendaftaran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KetuaSeeder extends Seeder
{
    public function run(): void
    {
        // Tahun Ajaran + Kelas
        $tahunAjaran = TahunAjaran::firstOrCreate(
            ['nama' => '2025/2026'],
            ['is_active' => true]
        );

        $kelas = Kelas::firstOrCreate(
            ['nama' => 'X RPL 1', 'tingkat' => 'x', 'tahun_ajaran_id' => $tahunAjaran->id],
        );

        // User + Siswa (Ketua)
        $userKetua = User::create([
            'username' => 'ketua01',
            'email'    => 'ketua01@soul.test',
            'password' => Hash::make('password'),
            'role'     => 'siswa',
        ]);

        $siswa = $userKetua->siswa()->create([
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
            'nama'            => 'Pelatih Test',
            'jenis_kelamin'   => 'laki-laki',
            'no_hp'           => '081234567890',
        ]);

        // Ekskul
        $ekskul = Ekskul::create([
            'pembina_id'          => $pembina->id,
            'pelatih_id'          => $pelatih->id,
            'nama_ekskul'         => 'Karate',
            'deskripsi'           => 'Ekstrakurikuler bela diri karate',
            'jadwal'              => 'Senin & Rabu, 15:30 - 17:00',
            'is_open_recruitment' => true,
        ]);

        // Pendaftaran Ketua → Ekskul
        Pendaftaran::create([
            'siswa_id'       => $siswa->id,
            'ekskul_id'      => $ekskul->id,
            'tanggal_daftar' => now()->toDateString(),
            'status'         => 'diterima',
        ]);
    }
}
