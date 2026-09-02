<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Ekskul;
use App\Models\Pelatih;
use App\Models\Pembina;
use App\Models\Pendaftaran;
use App\Models\Kegiatan;
use App\Models\PengajuanKeluar;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Models\Prestasi;
use App\Models\Testimoni;
use App\Models\Faq;
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

        // === PEMBINA KARATE ===
        $userPembinaKarate = User::create([
            'username' => 'pembina_karate',
            'email'    => 'pembina_karate@soul.test',
            'password' => Hash::make('password'),
            'role'     => 'pembina',
        ]);

        $pembinaKarate = Pembina::create([
            'user_id'         => $userPembinaKarate->id,
            'nip'             => '198501012020011001',
            'nama'            => 'Pak Ahmad, S.Pd',
            'jenis_kelamin'   => 'laki-laki',
        ]);

        // === PEMBINA PASKIBRA ===
        $userPembinaPaskibra = User::create([
            'username' => 'pembina_paskibra',
            'email'    => 'pembina_paskibra@soul.test',
            'password' => Hash::make('password'),
            'role'     => 'pembina',
        ]);

        $pembinaPaskibra = Pembina::create([
            'user_id'         => $userPembinaPaskibra->id,
            'nip'             => '198502022020022002',
            'nama'            => 'Bu Siti, S.Pd',
            'jenis_kelamin'   => 'perempuan',
        ]);

        // === EKSKUL 1 - KARATE ===
        $ekskul1 = Ekskul::create([
            'pembina_id'          => $pembinaKarate->id,
            'pelatih_id'          => $pelatih1->id,
            'nama_ekskul'         => 'Karate',
            'tagline'             => 'Disiplin, Tangguh, dan Percaya Diri.',
            'deskripsi'           => 'Ekstrakurikuler bela diri karate untuk membentuk karakter disiplin dan tangguh',
            'tujuan'              => 'Mengembangkan kemampuan bela diri, melatih kedisiplinan, serta membangun jiwa tangguh dan percaya diri bagi setiap anggota.',
            'jadwal'              => 'Senin & Rabu, 15:30 - 17:00',
            'is_open_recruitment' => true,
        ]);

        // === EKSKUL 2 - PASKIBRA ===
        $ekskul2 = Ekskul::create([
            'pembina_id'          => $pembinaPaskibra->id,
            'pelatih_id'          => $pelatih2->id,
            'nama_ekskul'         => 'Paskibra',
            'tagline'             => 'Berkibar Tinggi, Berkarakter Kuat.',
            'deskripsi'           => 'Pasukan Pengibar Bendera - Ekskul prestisius di sekolah',
            'tujuan'              => 'Melatih kedisiplinan, kepemimpinan, dan rasa cinta tanah air melalui latihan baris-berbaris dan upacara.',
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
            'alasan'         => 'Ingin mengembangkan bakat bela diri karate.',
        ]);

        // === SISWA KETUA PASKIBRA ===
        $userKetuaPaskibra = User::create([
            'username' => 'ketua_paskibra',
            'email'    => 'ketua_paskibra@soul.test',
            'password' => Hash::make('password'),
            'role'     => 'siswa',
        ]);

        $ketuaPaskibra = Siswa::create([
            'user_id'       => $userKetuaPaskibra->id,
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

        // === SISWA ANGGOTA 1 (TERDAFTAR KARATE) ===
        $userAnggota1 = User::create([
            'username' => 'siswa',
            'email'    => 'siswa@soul.test',
            'password' => Hash::make('password'),
            'role'     => 'siswa',
        ]);

        $anggota1 = Siswa::create([
            'user_id'       => $userAnggota1->id,
            'nis'           => '2406510002',
            'nama'          => 'Nazwa Nurhafiza',
            'kelas_id'      => $kelas['XII RPL 1']->id,
            'jenis_kelamin' => 'perempuan',
            'jabatan'       => 'anggota',
        ]);

        $pendaftaranSiswa = Pendaftaran::create([
            'siswa_id'       => $anggota1->id,
            'ekskul_id'      => $ekskul1->id,
            'tanggal_daftar' => now()->subDays(5)->toDateString(),
            'status'         => 'diterima',
            'alasan'         => 'Ingin belajar bela diri karate.',
        ]);

        // === SISWA ANGGOTA 2 (TERDAFTAR PASKIBRA) ===
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
            'alasan'         => 'Ingin menjadi pengibar bendera.',
        ]);

        // === SISWA PENDING ===
        $userPending = User::create([
            'username' => 'siswa01',
            'email'    => 'siswa01@soul.test',
            'password' => Hash::make('password'),
            'role'     => 'siswa',
        ]);

        $pending = Siswa::create([
            'user_id'       => $userPending->id,
            'nis'           => '2406510004',
            'nama'          => 'Andi Wijaya',
            'kelas_id'      => $kelas['XI RPL 2']->id,
            'jenis_kelamin' => 'laki-laki',
            'jabatan'       => 'anggota',
        ]);

        Pendaftaran::create([
            'siswa_id'       => $pending->id,
            'ekskul_id'      => $ekskul1->id,
            'tanggal_daftar' => now()->toDateString(),
            'status'         => 'pending',
            'alasan'         => 'Ingin bergabung dengan karate.',
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

        // === KEGIATAN ===
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

        // === PRESENSI ===
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

        // === PENGAJUAN KELUAR ===
        PengajuanKeluar::create([
            'siswa_id'          => $anggota2->id,
            'ekskul_id'         => $ekskul2->id,
            'alasan'            => 'Fokus pada pelajaran',
            'status'            => 'pending',
            'tanggal_pengajuan' => now()->toDateString(),
        ]);

        // === PRESTASI EKSKUL 1 (KARATE) ===
        Prestasi::create([
            'ekskul_id' => $ekskul1->id,
            'judul'     => 'Juara 1',
            'kategori'  => 'Kejuaraan Karate Jawa Barat',
            'tahun'     => '2025',
        ]);

        Prestasi::create([
            'ekskul_id' => $ekskul1->id,
            'judul'     => 'Juara 2',
            'kategori'  => 'POPKAB Karate Kota Bandung',
            'tahun'     => '2024',
        ]);

        Prestasi::create([
            'ekskul_id' => $ekskul1->id,
            'judul'     => 'Best Performer',
            'kategori'  => 'Festival Seni Beladiri',
            'tahun'     => '2025',
        ]);

        // === TESTIMONI EKSKUL 1 (KARATE) ===
        Testimoni::create([
            'ekskul_id' => $ekskul1->id,
            'nama'      => 'Aulia',
            'kelas'     => 'XI IPA 2',
            'quote'     => 'Awalnya saya ikut karena penasaran, tapi akhirnya dapat banyak teman dan pengalaman baru.',
        ]);

        Testimoni::create([
            'ekskul_id' => $ekskul1->id,
            'nama'      => 'Bima',
            'kelas'     => 'X RPL 1',
            'quote'     => 'Latihannya seru dan pembinanya sabar banget. Jadi makin disiplin setiap hari.',
        ]);

        Testimoni::create([
            'ekskul_id' => $ekskul1->id,
            'nama'      => 'Citra',
            'kelas'     => 'XII RPL 1',
            'quote'     => 'Karate mengajarkan saya percaya diri tampil di depan umum.',
        ]);

        // === FAQ EKSKUL 1 (KARATE) ===
        Faq::create([
            'ekskul_id'  => $ekskul1->id,
            'pertanyaan' => 'Apakah harus punya pengalaman sebelumnya?',
            'jawaban'    => 'Tidak, pemula juga boleh bergabung. Semua materi dimulai dari dasar.',
        ]);

        Faq::create([
            'ekskul_id'  => $ekskul1->id,
            'pertanyaan' => 'Apakah ada biaya pendaftaran?',
            'jawaban'    => 'Pendaftaran gratis. Tidak ada biaya untuk mengikuti latihan rutin.',
        ]);

        Faq::create([
            'ekskul_id'  => $ekskul1->id,
            'pertanyaan' => 'Apakah wajib mengikuti semua latihan?',
            'jawaban'    => 'Diharapkan hadir di setiap latihan. Jika berhalangan, wajib izin terlebih dahulu.',
        ]);

        Faq::create([
            'ekskul_id'  => $ekskul1->id,
            'pertanyaan' => 'Kapan pendaftaran dibuka?',
            'jawaban'    => 'Lihat status pendaftaran di halaman ekskul. Jika masih "Pendaftaran Dibuka", kamu bisa langsung mendaftar.',
        ]);

        // === PRESTASI EKSKUL 2 (PASKIBRA) ===
        Prestasi::create([
            'ekskul_id' => $ekskul2->id,
            'judul'     => 'Juara 1',
            'kategori'  => 'Lomba Paskibra Tingkat Kota',
            'tahun'     => '2025',
        ]);

        Prestasi::create([
            'ekskul_id' => $ekskul2->id,
            'judul'     => 'Juara Harapan 1',
            'kategori'  => 'Paskibra Competition Jawa Barat',
            'tahun'     => '2024',
        ]);

        // === TESTIMONI EKSKUL 2 (PASKIBRA) ===
        Testimoni::create([
            'ekskul_id' => $ekskul2->id,
            'nama'      => 'Dewi',
            'kelas'     => 'XII IPA 1',
            'quote'     => 'Paskibra mengajarkan saya arti tanggung jawab dan kekompakan tim.',
        ]);

        Testimoni::create([
            'ekskul_id' => $ekskul2->id,
            'nama'      => 'Eko',
            'kelas'     => 'XI RPL 2',
            'quote'     => 'Latihan baris-berbaris terasa berat, tapi hasilnya membanggakan.',
        ]);

        // === FAQ EKSKUL 2 (PASKIBRA) ===
        Faq::create([
            'ekskul_id'  => $ekskul2->id,
            'pertanyaan' => 'Apakah harus memiliki fisik yang kuat?',
            'jawaban'    => 'Tidak harus, yang penting kemauan untuk belajar dan berlatih.',
        ]);

        Faq::create([
            'ekskul_id'  => $ekskul2->id,
            'pertanyaan' => 'Apakah ada seragam khusus?',
            'jawaban'    => 'Ya, seragam Paskibra disediakan oleh sekolah untuk anggota yang sudah diterima.',
        ]);

        $this->command->info('✅ Database berhasil diisi dengan data dummy!');
        $this->command->info('');
        $this->command->info('📧 AKUN LOGIN:');
        $this->command->info('   ┌───────────────────┬──────────────────────────────┬───────────┐');
        $this->command->info('   │ Role              │ Email                        │ Password  │');
        $this->command->info('   ├───────────────────┼──────────────────────────────┼───────────┤');
        $this->command->info('   │ Admin             │ admin@soul.test              │ password  │');
        $this->command->info('   │ Kesiswaan         │ kesiswaan@soul.test          │ password  │');
        $this->command->info('   │ Pembina Karate    │ pembina_karate@soul.test     │ password  │');
        $this->command->info('   │ Pembina Paskibra  │ pembina_paskibra@soul.test   │ password  │');
        $this->command->info('   │ Siswa (Terdaftar) │ siswa@soul.test              │ password  │');
        $this->command->info('   │ Siswa (Baru)      │ siswa_baru@soul.test         │ password  │');
        $this->command->info('   │ Ketua Karate      │ ketua01@soul.test            │ password  │');
        $this->command->info('   │ Ketua Paskibra    │ ketua_paskibra@soul.test     │ password  │');
        $this->command->info('   │ Siswa Pending     │ siswa01@soul.test            │ password  │');
        $this->command->info('   └───────────────────┴──────────────────────────────┴───────────┘');
        $this->command->info('');
        $this->command->info('🔑 Login dengan siswa_baru@soul.test untuk melihat tombol daftar ekskul!');
    }
}