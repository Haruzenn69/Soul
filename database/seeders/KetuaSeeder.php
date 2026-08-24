<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Pembina;
use App\Models\Pelatih;
use App\Models\Ekskul;
use App\Models\Pendaftaran;
use App\Models\Kegiatan;
use App\Models\PengajuanKeluar;
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

        // User + Siswa (Anggota 1)
        $userAnggota1 = User::create([
            'username' => 'anggota01',
            'email'    => 'anggota01@soul.test',
            'password' => Hash::make('password'),
            'role'     => 'siswa',
        ]);

        $anggota1 = $userAnggota1->siswa()->create([
            'nis'           => '2406510002',
            'nama'          => 'Budi Santoso',
            'kelas_id'      => $kelas->id,
            'jenis_kelamin' => 'laki-laki',
            'jabatan'       => 'anggota',
        ]);

        // User + Siswa (Anggota 2)
        $userAnggota2 = User::create([
            'username' => 'anggota02',
            'email'    => 'anggota02@soul.test',
            'password' => Hash::make('password'),
            'role'     => 'siswa',
        ]);

        $anggota2 = $userAnggota2->siswa()->create([
            'nis'           => '2406510003',
            'nama'          => 'Siti Rahayu',
            'kelas_id'      => $kelas->id,
            'jenis_kelamin' => 'perempuan',
            'jabatan'       => 'anggota',
        ]);

        // User + Siswa (Pending)
        $userPending = User::create([
            'username' => 'pending01',
            'email'    => 'pending01@soul.test',
            'password' => Hash::make('password'),
            'role'     => 'siswa',
        ]);

        $userPending->siswa()->create([
            'nis'           => '2406510004',
            'nama'          => 'Andi Wijaya',
            'kelas_id'      => $kelas->id,
            'jenis_kelamin' => 'laki-laki',
            'jabatan'       => 'siswa',
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

        // Pendaftaran Ketua → Ekskul (diterima)
        Pendaftaran::create([
            'siswa_id'       => $siswa->id,
            'ekskul_id'      => $ekskul->id,
            'tanggal_daftar' => now()->toDateString(),
            'status'         => 'diterima',
        ]);

        // Pendaftaran Anggota 1 → Ekskul (diterima)
        Pendaftaran::create([
            'siswa_id'       => $anggota1->id,
            'ekskul_id'      => $ekskul->id,
            'tanggal_daftar' => now()->subDays(5)->toDateString(),
            'status'         => 'diterima',
        ]);

        // Pendaftaran Anggota 2 → Ekskul (diterima)
        Pendaftaran::create([
            'siswa_id'       => $anggota2->id,
            'ekskul_id'      => $ekskul->id,
            'tanggal_daftar' => now()->subDays(3)->toDateString(),
            'status'         => 'diterima',
        ]);

        // Pendaftaran Pending → Ekskul (pending)
        Pendaftaran::create([
            'siswa_id'       => \App\Models\Siswa::where('nis', '2406510004')->first()->id,
            'ekskul_id'      => $ekskul->id,
            'tanggal_daftar' => now()->toDateString(),
            'status'         => 'pending',
        ]);

        // Kegiatan
        $kegiatan = Kegiatan::create([
            'ekskul_id'         => $ekskul->id,
            'materi'            => 'Tendangan Dasar',
            'tanggal_kegiatan'  => now()->subDays(2)->toDateString(),
        ]);

        // Pengajuan Keluar (pending)
        PengajuanKeluar::create([
            'siswa_id'           => $anggota1->id,
            'ekskul_id'          => $ekskul->id,
            'alasan'             => 'Fokus pada pelajaran',
            'status'             => 'pending',
            'tanggal_pengajuan'  => now()->toDateString(),
        ]);
    }
}
