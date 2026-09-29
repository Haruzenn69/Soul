<?php

namespace Database\Seeders;

use App\Models\Ekskul;
use App\Models\Faq;
use App\Models\Kegiatan;
use App\Models\Kelas;
use App\Models\Pelatih;
use App\Models\Pembina;
use App\Models\Pendaftaran;
use App\Models\PengajuanKeluar;
use App\Models\Presensi;
use App\Models\Prestasi;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Testimoni;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Tahun Ajaran
        $tahunAjaran = TahunAjaran::create([
            'nama' => '2026/2027',
            'is_active' => true,
        ]);

        // Kelas
        $kelas = [];
        $kelasData = [
            ['10 PPLG 1', 'x', 'rpl', '1'],
            ['10 PPLG 2', 'x', 'rpl', '2'],
            ['11 RPL 1', 'xi', 'rpl', '1'],
            ['11 RPL 2', 'xi', 'rpl', '2'],
            ['12 RPL 1', 'xii', 'rpl', '1'],
            ['12 RPL 2', 'xii', 'rpl', '2'],
        ];

        foreach ($kelasData as [$nama, $tingkat, $jurusan, $rombel]) {
            $kelas[$nama] = Kelas::create([
                'nama' => $nama,
                'tingkat' => $tingkat,
                'jurusan' => $jurusan,
                'rombel' => $rombel,
                'tahun_ajaran_id' => $tahunAjaran->id,
            ]);
        }

        // Pelatih
        $pelatih1 = Pelatih::create([
            'nama' => 'Budi Hartono',
            'jenis_kelamin' => 'laki-laki',
            'no_hp' => '081234567890',
            'status' => 'aktif',
        ]);

        $pelatih2 = Pelatih::create([
            'nama' => 'Dewi Lestari',
            'jenis_kelamin' => 'perempuan',
            'no_hp' => '081298765432',
            'status' => 'aktif',
        ]);

        // Admin
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'email' => 'admin@soul.test',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Kesiswaan
        User::firstOrCreate(
            ['username' => 'kesiswaan'],
            [
                'email' => 'kesiswaan@soul.test',
                'password' => Hash::make('password'),
                'role' => 'kesiswaan',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ Database berhasil diinisialisasi dengan data master (Tahun Ajaran, Kelas, Pelatih) dan Akun Admin & Kesiswaan.');
    }
}
