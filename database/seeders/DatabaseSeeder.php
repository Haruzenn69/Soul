<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Ekskul;
use App\Models\Pelatih;
use App\Models\Pembina;
use App\Models\Pendaftaran;
use App\Models\Kegiatan;
use App\Models\PengajuanKeluar;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Presensi;
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
            ['X RPL 1', 'x'],
            ['X RPL 2', 'x'],
            ['XI RPL 1', 'xi'],
            ['XI RPL 2', 'xi'],
            ['XII RPL 1', 'xii'],
            ['XII RPL 2', 'xii']
        ];
        
        foreach ($kelasData as [$nama, $tingkat]) {
            $kelas[$nama] = Kelas::create([
                'nama' => $nama,
                'tingkat' => $tingkat,
                'tahun_ajaran_id' => $tahunAjaran->id,
            ]);
        }

        // Pelatih
        $pelatih1 = Pelatih::create([
            'nama'            => 'Budi Hartono',
            'jenis_kelamin'   => 'laki-laki',
            'no_hp'           => '081234567890',
            'status'          => 'aktif',
        ]);

        $pelatih2 = Pelatih::create([
            'nama'            => 'Siti Maryam',
            'jenis_kelamin'   => 'perempuan',
            'no_hp'           => '081234567899',
            'status'          => 'aktif',
        ]);

        // Admin
        User::create([
            'username' => 'admin',
            'email'    => 'admin@soul.test',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // Kesiswaan
        User::create([
            'username' => 'kesiswaan',
            'email'    => 'kesiswaan@soul.test',
            'password' => Hash::make('password'),
            'role'     => 'kesiswaan',
        ]);

        // Pembina
        $userPembina = User::create([
            'username' => 'pembina01',
            'email'    => 'pembina01@soul.test',
            'password' => Hash::make('password'),
            'role'     => 'pembina',
        ]);

        $pembina = Pembina::create([
            'user_id'         => $userPembina->id,
            'nip'             => '198501012020011001',
            'nama'            => 'Drs. Ahmad Fauzi, M.Pd',
            'jenis_kelamin'   => 'laki-laki',
        ]);

        // Ekskul 1
        $ekskul1 = Ekskul::create([
            'pembina_id'          => $pembina->id,
            'pelatih_id'          => $pelatih1->id,
            'nama_ekskul'         => 'Karate',
            'deskripsi'           => 'Ekstrakurikuler bela diri karate',
            'jadwal'              => 'Senin & Rabu, 15:30 - 17:00',
            'is_open_recruitment' => true,
        ]);

        // Ekskul 2
        $ekskul2 = Ekskul::create([
            'pembina_id'          => $pembina->id,
            'pelatih_id'          => $pelatih2->id,
            'nama_ekskul'         => 'Paskibra',
            'deskripsi'           => 'Pasukan Pengibar Bendera',
            'jadwal'              => 'Selasa & Jumat, 15:30 - 17:30',
            'is_open_recruitment' => true,
        ]);

        // Siswa Ketua
        $userKetua = User::create([
            'username' => 'ketua01',
            'email'    => 'ketua01@soul.test',
            'password' => Hash::make('password'),
            'role'     => 'siswa',
        ]);

        $ketua = Siswa::create([
            'user_id'       => $userKetua->id,
            'nis'           => '2406510001',
            'nama'          => 'Rizki Pratama',
            'kelas_id'      => $kelas['XII RPL 1']->id,
            'jenis_kelamin' => 'laki-laki',
            'jabatan'       => 'ketua',
        ]);

        Pendaftaran::create([
            'siswa_id'       => $ketua->id,
            'ekskul_id'      => $ekskul1->id,
            'tanggal_daftar' => now()->subDays(10)->toDateString(),
            'status'         => 'diterima',
        ]);

        // Siswa Anggota 1 (UNTUK LOGIN SISWA)
        $userSiswa = User::create([
            'username' => 'siswa',
            'email'    => 'siswa@soul.test',
            'password' => Hash::make('password'),
            'role'     => 'siswa',
        ]);

        $siswa = Siswa::create([
            'user_id'       => $userSiswa->id,
            'nis'           => '2406510002',
            'nama'          => 'Nazwa Nurhafiza',
            'kelas_id'      => $kelas['XII RPL 1']->id,
            'jenis_kelamin' => 'perempuan',
            'jabatan'       => 'anggota',
        ]);

        $pendaftaranSiswa = Pendaftaran::create([
            'siswa_id'       => $siswa->id,
            'ekskul_id'      => $ekskul1->id,
            'tanggal_daftar' => now()->subDays(5)->toDateString(),
            'status'         => 'diterima',
        ]);

        // Siswa Anggota 2
        $userAnggota2 = User::create([
            'username' => 'anggota02',
            'email'    => 'anggota02@soul.test',
            'password' => Hash::make('password'),
            'role'     => 'siswa',
        ]);

        $anggota2 = Siswa::create([
            'user_id'       => $userAnggota2->id,
            'nis'           => '2406510003',
            'nama'          => 'Siti Rahayu',
            'kelas_id'      => $kelas['XI RPL 1']->id,
            'jenis_kelamin' => 'perempuan',
            'jabatan'       => 'anggota',
        ]);

        $pendaftaranAnggota2 = Pendaftaran::create([
            'siswa_id'       => $anggota2->id,
            'ekskul_id'      => $ekskul2->id,
            'tanggal_daftar' => now()->subDays(3)->toDateString(),
            'status'         => 'diterima',
        ]);

        // Kegiatan
        $kegiatan1 = Kegiatan::create([
            'ekskul_id'        => $ekskul1->id,
            'materi'           => 'Latihan Tendangan Dasar',
            'tanggal_kegiatan' => now()->addDays(2)->toDateString(),
        ]);

        $kegiatan2 = Kegiatan::create([
            'ekskul_id'        => $ekskul1->id,
            'materi'           => 'Latihan Bantingan Dasar',
            'tanggal_kegiatan' => now()->addDays(5)->toDateString(),
        ]);

        $kegiatan3 = Kegiatan::create([
            'ekskul_id'        => $ekskul1->id,
            'materi'           => 'Ujian Kenaikan Sabuk',
            'tanggal_kegiatan' => now()->addDays(10)->toDateString(),
        ]);

        // Presensi (pakai pendaftaran_id sesuai migration)
        Presensi::create([
            'kegiatan_id'     => $kegiatan1->id,
            'pendaftaran_id'  => $pendaftaranSiswa->id,
            'status'          => 'hadir',
        ]);

        Presensi::create([
            'kegiatan_id'     => $kegiatan2->id,
            'pendaftaran_id'  => $pendaftaranSiswa->id,
            'status'          => 'hadir',
        ]);

        Presensi::create([
            'kegiatan_id'     => $kegiatan3->id,
            'pendaftaran_id'  => $pendaftaranSiswa->id,
            'status'          => 'hadir',
        ]);

        // Pengajuan Keluar
        PengajuanKeluar::create([
            'siswa_id'          => $anggota2->id,
            'ekskul_id'         => $ekskul2->id,
            'alasan'            => 'Fokus pada pelajaran',
            'status'            => 'pending',
            'tanggal_pengajuan' => now()->toDateString(),
        ]);

        $this->command->info('✅ Database berhasil diisi!');
        $this->command->info('');
        $this->command->info('📧 AKUN LOGIN:');
        $this->command->info('   Admin:     admin@soul.test     / password');
        $this->command->info('   Kesiswaan: kesiswaan@soul.test / password');
        $this->command->info('   Pembina:   pembina01@soul.test / password');
        $this->command->info('   Siswa:     siswa@soul.test     / password');
        $this->command->info('   Ketua:     ketua01@soul.test   / password');
    }
}