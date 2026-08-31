<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Ekskul;
use App\Models\Pelatih;
use App\Models\Pendaftaran;
use App\Models\Kegiatan;
use App\Models\PengajuanKeluar;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\TahunAjaran;
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
        foreach ([['X RPL 1', 'x'], ['XI RPL 1', 'xi'], ['XI RPL 2', 'xi'], ['XII RPL 1', 'xii']] as [$nama, $tingkat]) {
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
            'nama'            => 'Dewi Lestari',
            'jenis_kelamin'   => 'perempuan',
            'no_hp'           => '081298765432',
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

        $pembina = $userPembina->pembina()->create([
            'nip'             => '198501012020011001',
            'nama'            => 'Pak Ahmad, S.Pd',
            'jenis_kelamin'   => 'laki-laki',
        ]);

        // Ekskul 1 - Karate
        $ekskul1 = Ekskul::create([
            'pembina_id'          => $pembina->id,
            'pelatih_id'          => $pelatih1->id,
            'nama_ekskul'         => 'Karate',
            'deskripsi'           => 'Ekstrakurikuler bela diri karate untuk membentuk karakter disiplin dan tangguh',
            'jadwal'              => 'Senin & Rabu, 15:30 - 17:00',
            'is_open_recruitment' => true,
        ]);

        // Ekskul 2 - Paskibra
        $ekskul2 = Ekskul::create([
            'pembina_id'          => $pembina->id,
            'pelatih_id'          => $pelatih2->id,
            'nama_ekskul'         => 'Paskibra',
            'deskripsi'           => 'Pasukan Pengibar Bendera - Ekskul prestisius di sekolah',
            'jadwal'              => 'Selasa & Jumat, 15:30 - 17:30',
            'is_open_recruitment' => true,
        ]);

        // === SISWA KETUA KARATE ===
        $userKetua = User::create([
            'username' => 'ketua01',
            'email'    => 'ketua01@soul.test',
            'password' => Hash::make('password'),
            'role'     => 'siswa',
        ]);

        $ketua = $userKetua->siswa()->create([
            'nis'           => '2406510001',
            'nama'          => 'Rizki Pratama',
            'kelas_id'      => $kelas['X RPL 1']->id,
            'jenis_kelamin' => 'laki-laki',
            'jabatan'       => 'ketua',
        ]);

        Pendaftaran::create([
            'siswa_id'       => $ketua->id,
            'ekskul_id'      => $ekskul1->id,
            'tanggal_daftar' => now()->subDays(10)->toDateString(),
            'status'         => 'diterima',
            'alasan'         => 'Ingin mengembangkan bakat bela diri karate.',
        ]);

        // === SISWA KETUA PASKIBRA ===
        $userKetuaPaskibra = User::create([
            'username' => 'ketua_paskibra',
            'email'    => 'ketua_paskibra@soul.test',
            'password' => Hash::make('password'),
            'role'     => 'siswa',
        ]);

        $ketuaPaskibra = $userKetuaPaskibra->siswa()->create([
            'nis'           => '2406510010',
            'nama'          => 'Siti Nurhaliza',
            'kelas_id'      => $kelas['XI RPL 1']->id,
            'jenis_kelamin' => 'perempuan',
            'jabatan'       => 'ketua',
        ]);

        Pendaftaran::create([
            'siswa_id'       => $ketuaPaskibra->id,
            'ekskul_id'      => $ekskul2->id,
            'tanggal_daftar' => now()->subDays(12)->toDateString(),
            'status'         => 'diterima',
            'alasan'         => 'Ingin memimpin ekskul Paskibra dengan disiplin dan tangguh.',
        ]);

        // === SISWA ANGGOTA 1 ===
        $userAnggota1 = User::create([
            'username' => 'siswa',
            'email'    => 'siswa@soul.test',
            'password' => Hash::make('password'),
            'role'     => 'siswa',
        ]);

        $anggota1 = Siswa::create([
            'user_id'       => $userAnggota1->id,
            'nis'           => '2406510002',
            'nama'          => 'Budi Santoso',
            'kelas_id'      => $kelas['X RPL 1']->id,
            'jenis_kelamin' => 'laki-laki',
            'jabatan'       => 'anggota',
        ]);

        Pendaftaran::create([
            'siswa_id'       => $anggota1->id,
            'ekskul_id'      => $ekskul1->id,
            'tanggal_daftar' => now()->subDays(5)->toDateString(),
            'status'         => 'diterima',
        ]);

        // === SISWA ANGGOTA 2 ===
        $userAnggota2 = User::create([
            'username' => 'anggota02',
            'email'    => 'anggota02@soul.test',
            'password' => Hash::make('password'),
            'role'     => 'siswa',
        ]);

        $anggota2 = $userAnggota2->siswa()->create([
            'nis'           => '2406510003',
            'nama'          => 'Siti Rahayu',
            'kelas_id'      => $kelas['XI RPL 1']->id,
            'jenis_kelamin' => 'perempuan',
            'jabatan'       => 'anggota',
        ]);

        Pendaftaran::create([
            'siswa_id'       => $anggota2->id,
            'ekskul_id'      => $ekskul1->id,
            'tanggal_daftar' => now()->subDays(3)->toDateString(),
            'status'         => 'diterima',
        ]);

        // === SISWA PENDING ===
        $userPending = User::create([
            'username' => 'siswa01',
            'email'    => 'siswa01@soul.test',
            'password' => Hash::make('password'),
            'role'     => 'siswa',
        ]);

        $pending = $userPending->siswa()->create([
            'nis'           => '2406510004',
            'nama'          => 'Andi Wijaya',
            'kelas_id'      => $kelas['XI RPL 2']->id,
            'jenis_kelamin' => 'laki-laki',
            'jabatan'       => 'siswa',
        ]);

        Pendaftaran::create([
            'siswa_id'       => $pending->id,
            'ekskul_id'      => $ekskul1->id,
            'tanggal_daftar' => now()->toDateString(),
            'status'         => 'pending',
        ]);

        // === SISWA BARU (BELUM TERDAFTAR) ===
        $userBaru = User::create([
            'username' => 'siswa_baru',
            'email'    => 'siswa_baru@soul.test',
            'password' => Hash::make('password'),
            'role'     => 'siswa',
        ]);

        $siswaBaru = Siswa::create([
            'user_id'       => $userBaru->id,
            'nis'           => '2406510005',
            'nama'          => 'Ahmad Fauzi',
            'kelas_id'      => $kelas['XII RPL 1']->id,
            'jenis_kelamin' => 'laki-laki',
            'jabatan'       => 'anggota',
        ]);

        // === KEGIATAN (Ekskul 1 - Karate) ===
        $kegiatan1 = Kegiatan::create([
            'ekskul_id'        => $ekskul1->id,
            'materi'           => 'Latihan Tendangan Dasar',
            'tanggal_kegiatan' => now()->subDays(7)->toDateString(),
        ]);

        $kegiatan2 = Kegiatan::create([
            'ekskul_id'        => $ekskul1->id,
            'materi'           => 'Latihan Bantingan Dasar',
            'tanggal_kegiatan' => now()->subDays(2)->toDateString(),
        ]);

        $kegiatan3 = Kegiatan::create([
            'ekskul_id'        => $ekskul1->id,
            'materi'           => 'Ujian Kenaikan Sabuk',
            'tanggal_kegiatan' => now()->addDays(10)->toDateString(),
        ]);

        // === PRESENSI ===
        $pendaftaranKetua = Pendaftaran::where('siswa_id', $ketua->id)->where('ekskul_id', $ekskul1->id)->first();
        $pendaftaranAnggota1 = Pendaftaran::where('siswa_id', $anggota1->id)->where('ekskul_id', $ekskul1->id)->first();
        $pendaftaranAnggota2 = Pendaftaran::where('siswa_id', $anggota2->id)->where('ekskul_id', $ekskul1->id)->first();

        Presensi::create([
            'kegiatan_id'     => $kegiatan1->id,
            'pendaftaran_id'  => $pendaftaranKetua->id,
            'status'          => 'hadir',
        ]);

        Presensi::create([
            'kegiatan_id'     => $kegiatan1->id,
            'pendaftaran_id'  => $pendaftaranAnggota1->id,
            'status'          => 'hadir',
        ]);

        Presensi::create([
            'kegiatan_id'     => $kegiatan1->id,
            'pendaftaran_id'  => $pendaftaranAnggota2->id,
            'status'          => 'hadir',
        ]);

        Presensi::create([
            'kegiatan_id'     => $kegiatan2->id,
            'pendaftaran_id'  => $pendaftaranKetua->id,
            'status'          => 'hadir',
        ]);

        Presensi::create([
            'kegiatan_id'     => $kegiatan2->id,
            'pendaftaran_id'  => $pendaftaranAnggota1->id,
            'status'          => 'sakit',
        ]);

        Presensi::create([
            'kegiatan_id'     => $kegiatan2->id,
            'pendaftaran_id'  => $pendaftaranAnggota2->id,
            'status'          => 'hadir',
        ]);

        // === PENGAJUAN KELUAR ===
        PengajuanKeluar::create([
            'siswa_id'          => $anggota1->id,
            'ekskul_id'         => $ekskul1->id,
            'alasan'            => 'Fokus pada pelajaran semester',
            'status'            => 'pending',
            'tanggal_pengajuan' => now()->toDateString(),
        ]);
    }
}
