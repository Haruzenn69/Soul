<?php

namespace Database\Seeders;

use App\Models\Ekskul;
use App\Models\Faq;
use App\Models\Kegiatan;
use App\Models\Kelas;
use App\Models\Pelatih;
use App\Models\Pembina;
use App\Models\Pendaftaran;
use App\Models\Penilaian;
use App\Models\Presensi;
use App\Models\Prestasi;
use App\Models\RiwayatJabatan;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Testimoni;
use App\Models\User;
use App\Support\AturanEkskul;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // ---------------------------------------------------------------------
        // 1. BERSIHKAN DATA DUMMY LAMA (KECUALI ADMIN & KESISWAAN)
        // ---------------------------------------------------------------------
        Schema::disableForeignKeyConstraints();

        DB::table('presensis')->truncate();
        if (Schema::hasTable('presensi_pelatihs')) {
            DB::table('presensi_pelatihs')->truncate();
        }
        DB::table('kegiatans')->truncate();
        DB::table('penilaians')->truncate();
        DB::table('pengajuan_keluars')->truncate();
        DB::table('riwayat_jabatans')->truncate();
        DB::table('pendaftarans')->truncate();
        DB::table('prestasis')->truncate();
        DB::table('pelatihs')->truncate();
        DB::table('ekskuls')->truncate();
        if (Schema::hasTable('siswa_profile_histories')) {
            DB::table('siswa_profile_histories')->truncate();
        }
        if (Schema::hasTable('pembina_profile_histories')) {
            DB::table('pembina_profile_histories')->truncate();
        }
        DB::table('siswas')->truncate();
        DB::table('pembinas')->truncate();
        User::whereNotIn('username', ['admin', 'kesiswaan'])->delete();
        DB::table('kelas')->truncate();

        Schema::enableForeignKeyConstraints();

        $defaultPassword = Hash::make('password');

        // Pastikan akun Admin & Kesiswaan tetap ada
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'email' => 'admin@soul.test',
                'password' => $defaultPassword,
                'role' => 'admin',
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['username' => 'kesiswaan'],
            [
                'email' => 'kesiswaan@soul.test',
                'password' => $defaultPassword,
                'role' => 'kesiswaan',
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        // ---------------------------------------------------------------------
        // 2. TAHUN AJARAN AKTIF
        // ---------------------------------------------------------------------
        $tahunAjaran = TahunAjaran::firstOrCreate(
            ['nama' => '2026/2027'],
            ['status' => 'aktif']
        );
        if ($tahunAjaran->status !== 'aktif') {
            $tahunAjaran->update(['status' => 'aktif']);
        }

        // ---------------------------------------------------------------------
        // 3. KELAS SESUAI CONFIG('KELAS') & SISTEM KESISWAAN
        // ---------------------------------------------------------------------
        // Tingkat: x (10), xi (11), xii (12)
        // Jurusan:
        // x   : PPLG, TJKT, DKV, PM, AKL, MPLB
        // xi  : RPL,  TKJ,  DKV, BR, AK,  MP,   MLOG
        // xii : RPL,  TKJ,  DKV, BR, AK,  MP,   MLOG
        $tingkatList = ['x', 'xi', 'xii'];
        $rombelList  = ['1', '2'];

        $kelasList = [];
        foreach ($tingkatList as $tingkat) {
            $tLabel = config("kelas.tingkat.{$tingkat}");
            $availableJurusans = array_keys(array_filter(
                config('kelas.jurusan'),
                fn ($j) => isset($j[$tingkat])
            ));

            foreach ($availableJurusans as $jurusan) {
                $jLabel = config("kelas.jurusan.{$jurusan}.{$tingkat}");
                foreach ($rombelList as $rombel) {
                    $namaKelas = "{$tLabel} {$jLabel} {$rombel}";
                    $kelasList[] = Kelas::create([
                        'nama' => $namaKelas,
                        'tingkat' => $tingkat,
                        'jurusan' => $jurusan,
                        'rombel' => $rombel,
                        'tahun_ajaran_id' => $tahunAjaran->id,
                    ]);
                }
            }
        }

        // ---------------------------------------------------------------------
        // 4. PEMBINA (Sesuai Aturan Kategori & NIP 18 Digit)
        // ---------------------------------------------------------------------
        // Kategori Olahraga hanya boleh dibina oleh Pak Ahmad
        $pembinaConfig = [
            [
                'nama' => 'Drs. Pak Ahmad Fauzi, M.Pd',
                'jenis_kelamin' => 'laki-laki',
                'nip' => '198001012005011001',
                'username' => 'pb.olahraga',
                'email' => 'pb.olahraga@soul.test',
                'kategori' => 'Olahraga',
                'tempat_lahir' => 'Purwokerto',
                'tanggal_lahir' => '1980-01-01',
                'agama' => 'islam',
                'no_telp' => '081211110001',
                'alamat' => 'Jl. Merdeka No. 10, Bandung',
            ],
            [
                'nama' => 'Dra. Siti Rahayu, M.Sn',
                'jenis_kelamin' => 'perempuan',
                'nip' => '198207202006042002',
                'username' => 'pb.seni',
                'email' => 'pb.seni@soul.test',
                'kategori' => 'Seni',
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '1982-07-20',
                'agama' => 'islam',
                'no_telp' => '081211110002',
                'alamat' => 'Jl. Riau No. 15, Bandung',
            ],
            [
                'nama' => 'Ibu Rina Anggraeni, S.Pd',
                'jenis_kelamin' => 'perempuan',
                'nip' => '198504102009022003',
                'username' => 'pb.bahasa',
                'email' => 'pb.bahasa@soul.test',
                'kategori' => 'Bahasa',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1985-04-10',
                'agama' => 'islam',
                'no_telp' => '081211110003',
                'alamat' => 'Jl. Dago No. 45, Bandung',
            ],
            [
                'nama' => 'Bpk. Rudi Hartono, S.Pd',
                'jenis_kelamin' => 'laki-laki',
                'nip' => '198311052008011004',
                'username' => 'pb.lainnya',
                'email' => 'pb.lainnya@soul.test',
                'kategori' => 'Lainnya',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1983-11-05',
                'agama' => 'islam',
                'no_telp' => '081211110004',
                'alamat' => 'Jl. Cihampelas No. 20, Bandung',
            ],
            [
                'nama' => 'Ibu Dewi Kurniasih, S.Ag',
                'jenis_kelamin' => 'perempuan',
                'nip' => '198702282010012005',
                'username' => 'pb.dewi',
                'email' => 'pb.dewi@soul.test',
                'kategori' => null,
                'tempat_lahir' => 'Semarang',
                'tanggal_lahir' => '1987-02-28',
                'agama' => 'islam',
                'no_telp' => '081211110005',
                'alamat' => 'Jl. Buah Batu No. 8, Bandung',
            ],
            [
                'nama' => 'Bpk. Teguh Prasetyo, S.Kom',
                'jenis_kelamin' => 'laki-laki',
                'nip' => '198909152011011006',
                'username' => 'pb.teguh',
                'email' => 'pb.teguh@soul.test',
                'kategori' => null,
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1989-09-15',
                'agama' => 'kristen',
                'no_telp' => '081211110006',
                'alamat' => 'Jl. Setiabudi No. 12, Bandung',
            ],
        ];

        $pembinaMap = [];
        foreach ($pembinaConfig as $pData) {
            $user = User::create([
                'username' => $pData['username'],
                'email' => $pData['email'],
                'password' => $defaultPassword,
                'role' => 'pembina',
                'email_verified_at' => now(),
                'is_active' => true,
            ]);

            $pembina = Pembina::create([
                'user_id' => $user->id,
                'nip' => $pData['nip'],
                'nama' => $pData['nama'],
                'jenis_kelamin' => $pData['jenis_kelamin'],
                'tempat_lahir' => $pData['tempat_lahir'],
                'tanggal_lahir' => $pData['tanggal_lahir'],
                'agama' => $pData['agama'],
                'no_telp' => $pData['no_telp'],
                'alamat' => $pData['alamat'],
                'email' => $pData['email'],
                'kategori_pernah_dibina' => $pData['kategori'],
            ]);

            if ($pData['kategori']) {
                $pembinaMap[$pData['kategori']] = $pembina;
            }
        }

        // ---------------------------------------------------------------------
        // 5. EKSKUL SESUAI KATEGORI (Olahraga, Seni, Bahasa, Lainnya)
        // ---------------------------------------------------------------------
        $ekskulConfig = [
            // Kategori: Olahraga
            [
                'nama' => 'Futsal',
                'kategori' => 'Olahraga',
                'deskripsi' => 'Ekskul futsal melatih teknik mengolah bola, taktik menyerang dan bertahan, serta sportivitas kerja sama tim.',
                'tagline' => 'Satu Tim, Satu Tujuan',
                'tujuan' => 'Membentuk atlet futsal berprestasi, bermental juara, dan berjiwa sportif.',
                'jadwal' => 'Selasa & Kamis, 15:30 - 17:30',
                'open' => true,
                'pembina' => $pembinaMap['Olahraga']->id ?? null,
            ],
            [
                'nama' => 'Bola Basket',
                'kategori' => 'Olahraga',
                'deskripsi' => 'Ekskul bola basket mengasah kelincahan, akurasi tembakan, dan kekompakan strategi di lapangan.',
                'tagline' => 'Tinggi, Cepat, Presisi',
                'tujuan' => 'Mencetak atlet bola basket tangguh dan berprestasi di kompetisi pelajar.',
                'jadwal' => 'Senin & Rabu, 15:30 - 17:30',
                'open' => true,
                'pembina' => $pembinaMap['Olahraga']->id ?? null,
            ],
            [
                'nama' => 'Bulu Tangkis',
                'kategori' => 'Olahraga',
                'deskripsi' => 'Melatih refleks, footwork, smash tajam, dan stamina fisik atlet bulutangkis.',
                'tagline' => 'Smash dengan Semangat',
                'tujuan' => 'Mengembangkan bakat bulutangkis hingga tingkat kejuaraan daerah dan nasional.',
                'jadwal' => 'Kamis & Sabtu, 08:00 - 10:00',
                'open' => true,
                'pembina' => $pembinaMap['Olahraga']->id ?? null,
            ],
            [
                'nama' => 'Karate',
                'kategori' => 'Olahraga',
                'deskripsi' => 'Seni bela diri karate melatih konsentrasi, disiplin, teknik kata, dan kumite.',
                'tagline' => 'Disiplin dalam Setiap Gerakan',
                'tujuan' => 'Membentuk fisik yang kuat dan karakter kesatria yang berakhlak mulia.',
                'jadwal' => 'Rabu & Jumat, 15:30 - 17:30',
                'open' => false,
                'pembina' => $pembinaMap['Olahraga']->id ?? null,
            ],

            // Kategori: Seni
            [
                'nama' => 'Seni Tari',
                'kategori' => 'Seni',
                'deskripsi' => 'Melestarikan seni tari tradisional nusantara sekaligus mengeksplorasi kreasi modern kontemporer.',
                'tagline' => 'Gerak adalah Bahasa Jiwa',
                'tujuan' => 'Mengasah estetika, kepekaan ritme gerak, dan kecintaan pada budaya bangsa.',
                'jadwal' => 'Senin & Kamis, 15:00 - 17:00',
                'open' => true,
                'pembina' => $pembinaMap['Seni']->id ?? null,
            ],
            [
                'nama' => 'Paduan Suara',
                'kategori' => 'Seni',
                'deskripsi' => 'Melatih teknik olah vokal, harmonisasi paduan suara, dinamika nada, dan performa panggung.',
                'tagline' => 'Satu Suara, Satu Harmoni',
                'tujuan' => 'Mewujudkan paduan suara sekolah yang anggun, kompak, dan berprestasi.',
                'jadwal' => 'Selasa & Jumat, 15:00 - 17:00',
                'open' => true,
                'pembina' => $pembinaMap['Seni']->id ?? null,
            ],
            [
                'nama' => 'Band Sekolah',
                'kategori' => 'Seni',
                'deskripsi' => 'Wadah bermusik bersama, eksplorasi aransemen lagu, instrumen musik, dan kreativitas bermusik.',
                'tagline' => 'Musik adalah Ekspresi Bebas',
                'tujuan' => 'Menyalurkan bakat musikalitas siswa melalui karya orisinal dan pertunjukan panggung.',
                'jadwal' => 'Rabu, 15:00 - 17:30',
                'open' => false,
                'pembina' => $pembinaMap['Seni']->id ?? null,
            ],
            [
                'nama' => 'Sinematografi & Multimedia',
                'kategori' => 'Seni',
                'deskripsi' => 'Belajar videografi, tata kamera, penulisan skrip film pendek, editing digital, dan produksi konten visual.',
                'tagline' => 'Setiap Frame adalah Cerita',
                'tujuan' => 'Mencetak sineas dan kreator konten muda yang kreatif, kritis, dan berwawasan luas.',
                'jadwal' => 'Kamis, 15:00 - 17:30',
                'open' => true,
                'pembina' => $pembinaMap['Seni']->id ?? null,
            ],

            // Kategori: Bahasa
            [
                'nama' => 'English Club',
                'kategori' => 'Bahasa',
                'deskripsi' => 'Mengasah kemampuan berbahasa Inggris lewat debat, storytelling, speech contest, dan daily conversations.',
                'tagline' => 'Speak Up, Stand Out',
                'tujuan' => 'Meningkatkan rasa percaya diri dan kemahiran komunikasi internasional siswa.',
                'jadwal' => 'Selasa & Kamis, 15:00 - 16:30',
                'open' => true,
                'pembina' => $pembinaMap['Bahasa']->id ?? null,
            ],
            [
                'nama' => 'Japanese Club',
                'kategori' => 'Bahasa',
                'deskripsi' => 'Mempelajari bahasa Jepang, huruf Kanji/Hiragana/Katakana, budaya tradisional, dan kaiwa (percakapan).',
                'tagline' => 'Nihongo de Hanasou',
                'tujuan' => 'Membuka wawasan bahasa dan kebudayaan Jepang untuk persiapan studi dan minat global.',
                'jadwal' => 'Rabu & Jumat, 15:00 - 16:30',
                'open' => true,
                'pembina' => $pembinaMap['Bahasa']->id ?? null,
            ],

            // Kategori: Lainnya
            [
                'nama' => 'Paskibra',
                'kategori' => 'Lainnya',
                'deskripsi' => 'Membina kedisiplinan tingkat tinggi, postur baris-berbaris tegap, serta jiwa kepemimpinan nasionalis.',
                'tagline' => 'Tegak Berdiri untuk Nusa Bangsa',
                'tujuan' => 'Menyiapkan paskibraka andalan upacara kenegaraan dan berjiwa Pancasila.',
                'jadwal' => 'Senin & Jumat, 15:30 - 17:30',
                'open' => true,
                'pembina' => $pembinaMap['Lainnya']->id ?? null,
            ],
            [
                'nama' => 'Pramuka',
                'kategori' => 'Lainnya',
                'deskripsi' => 'Pendidikan kepanduan yang membentuk kemandirian, kecakapan bertahan hidup di alam, dan gotong royong.',
                'tagline' => 'Satyaku Kudarmakan, Darmaku Kubaktikan',
                'tujuan' => 'Membentuk generasi muda yang tangguh, terampil, dan berkarakter mulia.',
                'jadwal' => 'Sabtu, 07:30 - 11:30',
                'open' => true,
                'pembina' => $pembinaMap['Lainnya']->id ?? null,
            ],
            [
                'nama' => 'PMR (Palang Merah Remaja)',
                'kategori' => 'Lainnya',
                'deskripsi' => 'Pelatihan pertolongan pertama pada kecelakaan (P3K), kesiapsiagaan bencana, dan aksi donor darah kemanusiaan.',
                'tagline' => 'Siamo Tutti Fratelli',
                'tujuan' => 'Mencetak relawan muda yang tanggap darurat dan berjiwa empati tinggi.',
                'jadwal' => 'Rabu, 15:00 - 17:00',
                'open' => true,
                'pembina' => $pembinaMap['Lainnya']->id ?? null,
            ],
            [
                'nama' => 'Rohis (Rohani Islam)',
                'kategori' => 'Lainnya',
                'deskripsi' => 'Pembinaan akhlakul karimah, kajian keislaman, tadabbur Al-Quran, dan kepemimpinan dakwah sekolah.',
                'tagline' => 'Cerdas Berakhlak Mulia',
                'tujuan' => 'Membentuk generasi muslim cendekia yang kokoh iman dan ilmunya.',
                'jadwal' => 'Jumat, 12:30 - 14:30',
                'open' => true,
                'pembina' => $pembinaMap['Lainnya']->id ?? null,
            ],
            [
                'nama' => 'Tahfidz Al-Quran',
                'kategori' => 'Lainnya',
                'deskripsi' => 'Program tahfidz dan tahsin menghafal juz 30 dan juz pilihan dengan metode talaqqi dan murojaah rutin.',
                'tagline' => 'Hafizh Adalah Mahkota Keluarga',
                'tujuan' => 'Mendampingi siswa istiqomah menghafal firman Allah SWT.',
                'jadwal' => 'Senin & Kamis, 14:00 - 15:30',
                'open' => true,
                'pembina' => null, // Kesiswaan dapat menugaskan pembina kemudian
            ],
            [
                'nama' => 'Robotika',
                'kategori' => 'Lainnya',
                'deskripsi' => 'Perancangan sirkuit mikrokontroler Arduino/ESP32, mekanik robot, dan pemrograman IoT.',
                'tagline' => 'Inovasi Tanpa Batas',
                'tujuan' => 'Menciptakan inovasi teknologi terapan dan berkompetisi di kejuaraan robotika nasional.',
                'jadwal' => 'Selasa & Kamis, 15:00 - 17:30',
                'open' => true,
                'pembina' => null,
            ],
            [
                'nama' => 'KIR (Karya Ilmiah Remaja)',
                'kategori' => 'Lainnya',
                'deskripsi' => 'Metodologi penelitian ilmiah bidang sains terapan, sosial humaniora, dan penulisan karya inovatif.',
                'tagline' => 'Riset Hari Ini, Inovasi Masa Depan',
                'tujuan' => 'Menumbuhkan nalar kritis dan budaya meneliti di kalangan pelajar.',
                'jadwal' => 'Rabu & Jumat, 15:00 - 17:00',
                'open' => true,
                'pembina' => null,
            ],
            [
                'nama' => 'Tata Boga',
                'kategori' => 'Lainnya',
                'deskripsi' => 'Praktik pengolahan kuliner nusantara, pastry, bakery, hygiene sanitasi, dan dasar kewirausahaan makanan.',
                'tagline' => 'Masak dengan Cinta, Sajikan Bangga',
                'tujuan' => 'Membekali keterampilan memasak profesional dan wirausaha kuliner mandiri.',
                'jadwal' => 'Selasa & Sabtu, 09:00 - 12:00',
                'open' => true,
                'pembina' => null,
            ],
        ];

        $ekskuls = [];
        foreach ($ekskulConfig as $eData) {
            $ekskuls[] = Ekskul::create([
                'nama_ekskul' => $eData['nama'],
                'kategori' => $eData['kategori'],
                'deskripsi' => $eData['deskripsi'],
                'tagline' => $eData['tagline'],
                'tujuan' => $eData['tujuan'],
                'jadwal' => $eData['jadwal'],
                'is_open_recruitment' => $eData['open'],
                'status' => true,
                'pembina_id' => $eData['pembina'],
            ]);
        }

        // ---------------------------------------------------------------------
        // 6. PELATIH (1 Pelatih per Ekskul)
        // ---------------------------------------------------------------------
        $pelatihNames = [
            'Futsal' => ['Rizki Maulana', 'laki-laki', '082200000001'],
            'Bola Basket' => ['Andika Pratama', 'laki-laki', '082200000002'],
            'Bulu Tangkis' => ['Melinda Sari', 'perempuan', '082200000003'],
            'Karate' => ['Yusuf Hakim', 'laki-laki', '082200000004'],
            'Seni Tari' => ['Ratna Dewi', 'perempuan', '082200000005'],
            'Paduan Suara' => ['Hendri Wijaya', 'laki-laki', '082200000006'],
            'Band Sekolah' => ['Deni Firmansyah', 'laki-laki', '082200000007'],
            'Sinematografi & Multimedia' => ['Gilang Ramadhan', 'laki-laki', '082200000008'],
            'English Club' => ['Sarah Jessica', 'perempuan', '082200000009'],
            'Japanese Club' => ['Kenji Pratama', 'laki-laki', '082200000010'],
            'Paskibra' => ['Wahyu Setiawan', 'laki-laki', '082200000011'],
            'Pramuka' => ['Nur Hidayat', 'laki-laki', '082200000012'],
            'PMR (Palang Merah Remaja)' => ['Fitria Handayani', 'perempuan', '082200000013'],
            'Rohis (Rohani Islam)' => ['Ustadz Fadhlan', 'laki-laki', '082200000014'],
            'Tahfidz Al-Quran' => ['Ustadzah Maryam', 'perempuan', '082200000015'],
            'Robotika' => ['Budi Santoso', 'laki-laki', '082200000016'],
            'KIR (Karya Ilmiah Remaja)' => ['Intan Permata', 'perempuan', '082200000017'],
            'Tata Boga' => ['Sari Wulandari', 'perempuan', '082200000018'],
        ];

        foreach ($ekskuls as $ekskul) {
            if (isset($pelatihNames[$ekskul->nama_ekskul])) {
                [$namaPelatih, $jkPelatih, $hpPelatih] = $pelatihNames[$ekskul->nama_ekskul];
                Pelatih::create([
                    'nama' => $namaPelatih,
                    'jenis_kelamin' => $jkPelatih,
                    'no_hp' => $hpPelatih,
                    'domisili' => 'Bandung',
                    'status' => 'aktif',
                    'status_verifikasi' => 'terverifikasi',
                    'ekskul_id' => $ekskul->id,
                    'pembina_id' => $ekskul->pembina_id,
                ]);
            }
        }

        // ---------------------------------------------------------------------
        // 7. SISWA: 88 SISWA DENGAN NAMA BERBEDA (TIDAK ADA NAMA DUPLIKAT)
        // ---------------------------------------------------------------------
        // Total Akun: 1 Admin + 1 Kesiswaan + 6 Pembina + 88 Siswa = 96 Akun (~100 Akun)
        // Jumlah Siswa: 88 Siswa (rentang 80 sampai 90)
        $daftarNamaSiswa = [
            // Laki-laki (44 Siswa)
            ['nama' => 'Aldi Pratama Putra',      'jk' => 'laki-laki'],
            ['nama' => 'Bagas Saputra Wijaya',    'jk' => 'laki-laki'],
            ['nama' => 'Calvin Kurniawan',        'jk' => 'laki-laki'],
            ['nama' => 'Dimas Arya Perdana',      'jk' => 'laki-laki'],
            ['nama' => 'Erlangga Putra Dewa',     'jk' => 'laki-laki'],
            ['nama' => 'Farel Nugroho Wibowo',    'jk' => 'laki-laki'],
            ['nama' => 'Galang Ramadhan Kusuma',  'jk' => 'laki-laki'],
            ['nama' => 'Hendra Kusuma Jaya',      'jk' => 'laki-laki'],
            ['nama' => 'Ilham Maulana Yusuf',     'jk' => 'laki-laki'],
            ['nama' => 'Joko Susilo Hadiningrat', 'jk' => 'laki-laki'],
            ['nama' => 'Kevin Santoso Tan',       'jk' => 'laki-laki'],
            ['nama' => 'Luthfi Hakim Ananta',     'jk' => 'laki-laki'],
            ['nama' => 'Mahendra Dewa Saputra',   'jk' => 'laki-laki'],
            ['nama' => 'Naufal Aziz Mubarok',     'jk' => 'laki-laki'],
            ['nama' => 'Oscar Pratama Yudha',     'jk' => 'laki-laki'],
            ['nama' => 'Panji Wibowo Sukarno',    'jk' => 'laki-laki'],
            ['nama' => 'Raka Setiawan Nugraha',   'jk' => 'laki-laki'],
            ['nama' => 'Satrio Budi Santoso',     'jk' => 'laki-laki'],
            ['nama' => 'Tirta Yudha Perwira',     'jk' => 'laki-laki'],
            ['nama' => 'Vino Bastian Ramli',      'jk' => 'laki-laki'],
            ['nama' => 'Wahyu Hidayat Alamsyah',  'jk' => 'laki-laki'],
            ['nama' => 'Yoga Firmansyah Putra',   'jk' => 'laki-laki'],
            ['nama' => 'Zaki Ramadhan Mubarak',   'jk' => 'laki-laki'],
            ['nama' => 'Aditya Kurnia Mandiri',   'jk' => 'laki-laki'],
            ['nama' => 'Bima Sakti Pangestu',     'jk' => 'laki-laki'],
            ['nama' => 'Cahyo Nugroho Subroto',   'jk' => 'laki-laki'],
            ['nama' => 'Daffa Ardiansyah Putra',  'jk' => 'laki-laki'],
            ['nama' => 'Farhan Rizki Ramadhan',   'jk' => 'laki-laki'],
            ['nama' => 'Gilang Saputra Dirgantara','jk' => 'laki-laki'],
            ['nama' => 'Hafiz Maulana Shadiq',    'jk' => 'laki-laki'],
            ['nama' => 'Irfan Setiawan Prabowo',  'jk' => 'laki-laki'],
            ['nama' => 'Javier Ananta Wijaya',    'jk' => 'laki-laki'],
            ['nama' => 'Krisna Murti Pamungkas',  'jk' => 'laki-laki'],
            ['nama' => 'Leo Wijaya Suhendra',     'jk' => 'laki-laki'],
            ['nama' => 'Muhammad Fikri Baihaqi',  'jk' => 'laki-laki'],
            ['nama' => 'Nabil Makarim Pratama',   'jk' => 'laki-laki'],
            ['nama' => 'Oktavian Dwi Cahyono',    'jk' => 'laki-laki'],
            ['nama' => 'Pradana Putra Mahardika', 'jk' => 'laki-laki'],
            ['nama' => 'Rayhan Pratama Akbar',    'jk' => 'laki-laki'],
            ['nama' => 'Syahrul Gunawan Siregar', 'jk' => 'laki-laki'],
            ['nama' => 'Taufik Hidayat Wicaksono','jk' => 'laki-laki'],
            ['nama' => 'Umar Faruq Syarifudin',   'jk' => 'laki-laki'],
            ['nama' => 'Vito Raditya Hartanto',   'jk' => 'laki-laki'],
            ['nama' => 'Wildan Alamsyah Rasyid',  'jk' => 'laki-laki'],

            // Perempuan (44 Siswi)
            ['nama' => 'Alya Ramadhani Putri',    'jk' => 'perempuan'],
            ['nama' => 'Bella Agustina Kusuma',   'jk' => 'perempuan'],
            ['nama' => 'Citra Kirana Anggraini',  'jk' => 'perempuan'],
            ['nama' => 'Dinda Permata Sari',      'jk' => 'perempuan'],
            ['nama' => 'Elsa Maharani Pratiwi',   'jk' => 'perempuan'],
            ['nama' => 'Fany Setiawati Dewi',     'jk' => 'perempuan'],
            ['nama' => 'Gita Kumalasari Rahayu',  'jk' => 'perempuan'],
            ['nama' => 'Hana Safitri Nurhaliza',  'jk' => 'perempuan'],
            ['nama' => 'Indri Lestari Wulandari', 'jk' => 'perempuan'],
            ['nama' => 'Julia Susanti Handayani', 'jk' => 'perempuan'],
            ['nama' => 'Kania Rahayu Fitriani',   'jk' => 'perempuan'],
            ['nama' => 'Lita Anggraeni Maharani', 'jk' => 'perempuan'],
            ['nama' => 'Maya Wulandari Sukma',    'jk' => 'perempuan'],
            ['nama' => 'Nadia Putri Az-Zahra',    'jk' => 'perempuan'],
            ['nama' => 'Olivia Sucipto Wardani',  'jk' => 'perempuan'],
            ['nama' => 'Putri Nabila Khairunisa', 'jk' => 'perempuan'],
            ['nama' => 'Qorina Salsabila Maulida','jk' => 'perempuan'],
            ['nama' => 'Rini Andriani Pangestuti','jk' => 'perempuan'],
            ['nama' => 'Sinta Melinda Kusnadi',   'jk' => 'perempuan'],
            ['nama' => 'Tika Ratnasari Dewi',     'jk' => 'perempuan'],
            ['nama' => 'Ulfah Fitriani Hasanah',  'jk' => 'perempuan'],
            ['nama' => 'Vina Kusuma Wardhani',    'jk' => 'perempuan'],
            ['nama' => 'Widya Astuti Ningrum',    'jk' => 'perempuan'],
            ['nama' => 'Yuni Puspita Sari',       'jk' => 'perempuan'],
            ['nama' => 'Zahra Nurhaliza Putri',   'jk' => 'perempuan'],
            ['nama' => 'Amelia Sari Budiman',     'jk' => 'perempuan'],
            ['nama' => 'Bunga Nirmala Cantika',   'jk' => 'perempuan'],
            ['nama' => 'Cahaya Dewi Utami',       'jk' => 'perempuan'],
            ['nama' => 'Dita Purnama Ningrum',    'jk' => 'perempuan'],
            ['nama' => 'Eka Rahmawati Syahrini',  'jk' => 'perempuan'],
            ['nama' => 'Friska Amandari Putri',   'jk' => 'perempuan'],
            ['nama' => 'Hasna Khoiriyah Fitri',   'jk' => 'perempuan'],
            ['nama' => 'Iin Sulistiani Rahma',    'jk' => 'perempuan'],
            ['nama' => 'Jesika Putri Maharani',   'jk' => 'perempuan'],
            ['nama' => 'Karina Maharani Suwandi', 'jk' => 'perempuan'],
            ['nama' => 'Lestari Ayu Prameswari',  'jk' => 'perempuan'],
            ['nama' => 'Melati Indah Permatasari','jk' => 'perempuan'],
            ['nama' => 'Nurul Hidayati Shaliha',  'jk' => 'perempuan'],
            ['nama' => 'Pamela Safitri Ramadhani','jk' => 'perempuan'],
            ['nama' => 'Queeny Amanda Salsabila', 'jk' => 'perempuan'],
            ['nama' => 'Ratna Juwita Pertiwi',    'jk' => 'perempuan'],
            ['nama' => 'Safira Anandita Wijaya',  'jk' => 'perempuan'],
            ['nama' => 'Tari Wulandari Saputri',  'jk' => 'perempuan'],
            ['nama' => 'Ully Novita Anggraeni',   'jk' => 'perempuan'],
        ];

        $kotaList = ['Bandung', 'Jakarta', 'Surabaya', 'Semarang', 'Yogyakarta', 'Bogor', 'Depok', 'Bekasi', 'Cimahi', 'Cirebon'];
        $agamaList = ['islam', 'islam', 'islam', 'islam', 'kristen', 'katolik', 'hindu'];

        $allSiswa = [];
        $nisStart = 2026100001; // 10 digit NIS

        // Distribusi 88 siswa ke 36 kelas secara merata (2-3 siswa per kelas)
        $kelasCount = count($kelasList);
        foreach ($daftarNamaSiswa as $idx => $sData) {
            $currentKelas = $kelasList[$idx % $kelasCount];
            $nis = (string) ($nisStart + $idx);

            // Format email siswa yang bersih dan konsisten
            $cleanName = preg_replace('/[^a-z0-9]/', '.', strtolower($sData['nama']));
            $cleanName = trim(preg_replace('/\.+/', '.', $cleanName), '.');
            $email = $cleanName . '.' . substr($nis, -4) . '@student.soul.test';
            $username = 'sis' . $nis;

            $user = User::create([
                'username' => $username,
                'email' => $email,
                'password' => $defaultPassword,
                'role' => 'siswa',
                'email_verified_at' => now(),
                'is_active' => true,
            ]);

            // Tahun lahir disesuaikan dengan tingkat kelas (x: 2009-2010, xi: 2008-2009, xii: 2007-2008)
            $tahunLahir = match ($currentKelas->tingkat) {
                'x' => 2010 - ($idx % 2),
                'xi' => 2009 - ($idx % 2),
                'xii' => 2008 - ($idx % 2),
                default => 2009,
            };
            $bulanLahir = str_pad(($idx % 12) + 1, 2, '0', STR_PAD_LEFT);
            $hariLahir  = str_pad(($idx % 27) + 1, 2, '0', STR_PAD_LEFT);

            $siswa = Siswa::create([
                'user_id' => $user->id,
                'nis' => $nis,
                'nama' => $sData['nama'],
                'jenis_kelamin' => $sData['jk'],
                'kelas_id' => $currentKelas->id,
                'angkatan' => 2026,
                'status' => 'aktif',
                'tempat_lahir' => $kotaList[$idx % count($kotaList)],
                'tanggal_lahir' => "{$tahunLahir}-{$bulanLahir}-{$hariLahir}",
                'agama' => $agamaList[$idx % count($agamaList)],
                'email' => $email,
                'no_telp' => '0812' . str_pad($idx + 10000000, 8, '0', STR_PAD_LEFT),
                'alamat' => 'Jl. Pendidikan No. ' . ($idx + 1) . ', ' . $kotaList[$idx % count($kotaList)],
                'jabatan' => 'siswa',
            ]);

            $allSiswa[] = $siswa;
        }

        // ---------------------------------------------------------------------
        // 8. PENDAFTARAN EKSKUL (Maks 2 Ekskul & Mematuhi Aturan 4P1I)
        // ---------------------------------------------------------------------
        $ekskulCount = count($ekskuls);
        // Semua anggota ekskul aktif (status 'diterima') secara default.
        // Status 'peringatan' hanya ada jika diubah secara manual oleh ketua ekskul.
        $statusOptions = ['diterima', 'diterima', 'diterima', 'diterima', 'diterima', 'diterima', 'pending'];

        foreach ($allSiswa as $i => $siswa) {
            // Semua siswa memiliki 1 atau 2 ekskul
            $daftarCount = ($i % 3 === 0) ? 2 : 1;
            $sudahP1i = false;
            $sudahWajib = false;
            $terdaftar = [];

            // Urutkan pilihan ekskul berdasarkan index siswa agar distribusi merata
            for ($step = 0; $step < $ekskulCount && count($terdaftar) < $daftarCount; $step++) {
                $candidateEkskul = $ekskuls[($i + $step * 5) % $ekskulCount];

                $isWajib = $candidateEkskul->isWajib();
                if ($isWajib && $sudahWajib) {
                    continue;
                }

                $isP1i = AturanEkskul::isP1i($candidateEkskul->nama_ekskul);
                if ($isP1i && $sudahP1i) {
                    continue;
                }

                $status = $statusOptions[($i + $step) % count($statusOptions)];

                Pendaftaran::create([
                    'siswa_id' => $siswa->id,
                    'ekskul_id' => $candidateEkskul->id,
                    'tanggal_daftar' => Carbon::now()->subDays(20 + ($i % 60))->toDateString(),
                    'status' => $status,
                    'alasan' => null,
                ]);

                $terdaftar[] = $candidateEkskul->id;
                if ($isWajib) {
                    $sudahWajib = true;
                }
                if ($isP1i) {
                    $sudahP1i = true;
                }
            }
        }

        // ---------------------------------------------------------------------
        // 9. KETUA EKSKUL (1 Siswa Ketua per Ekskul & Riwayat Jabatan)
        // ---------------------------------------------------------------------
        foreach ($ekskuls as $ekskul) {
            $calonKetuaQuery = Pendaftaran::where('ekskul_id', $ekskul->id)
                ->where('status', 'diterima')
                ->whereHas('siswa', fn ($q) => $q->where('jabatan', 'siswa'))
                ->with('siswa');

            if ($ekskul->nama_ekskul === 'Seni Tari') {
                $calonKetua = (clone $calonKetuaQuery)
                    ->whereHas('siswa', fn ($q) => $q->where('jenis_kelamin', 'perempuan'))
                    ->first() ?? $calonKetuaQuery->first();
            } else {
                $calonKetua = $calonKetuaQuery->first();
            }

            if ($calonKetua && $calonKetua->siswa) {
                $calonKetua->siswa->update(['jabatan' => 'ketua']);

                RiwayatJabatan::create([
                    'siswa_id' => $calonKetua->siswa_id,
                    'ekskul_id' => $ekskul->id,
                    'jabatan' => RiwayatJabatan::JABATAN_KETUA,
                    'mulai' => Carbon::now()->subMonths(4)->toDateString(),
                    'selesai' => null,
                ]);
            }
        }

        // ---------------------------------------------------------------------
        // 10. KEGIATAN & PRESENSI LATIHAN
        // ---------------------------------------------------------------------
        $materiLatihan = [
            'Olahraga' => ['Latihan Fisik & Stamina', 'Penguasaan Teknik Dasar', 'Simulasi Taktik & Strategi', 'Evaluasi Permainan'],
            'Seni' => ['Latihan Harmonisasi & Vokal', 'Eksplorasi Gerak Tari', 'Gladi Resik Penampilan', 'Evaluasi Koreografi'],
            'Bahasa' => ['Speech & Public Speaking Workshop', 'Debate Practice Session', 'Grammar & Vocabulary Clinic', 'Culture Exchange Sharing'],
            'Lainnya' => ['Latihan Baris Berbaris Dasar', 'Simulasi Pertolongan Pertama', 'Kajian Rutin & Tadabbur', 'Workshop Perakitan Prototipe'],
        ];

        foreach ($ekskuls as $ekskul) {
            $poolMateri = $materiLatihan[$ekskul->kategori] ?? $materiLatihan['Lainnya'];
            $pendaftarans = Pendaftaran::where('ekskul_id', $ekskul->id)
                ->where('status', 'diterima')
                ->get();

            foreach ($poolMateri as $kIdx => $judulMateri) {
                $tanggalKegiatan = Carbon::now()->subDays(28 - ($kIdx * 7));

                $kegiatan = Kegiatan::create([
                    'ekskul_id' => $ekskul->id,
                    'materi' => "{$judulMateri} - {$ekskul->nama_ekskul}",
                    'jenis_kegiatan' => 'latihan',
                    'deskripsi' => "Sesi latihan rutin berkala untuk meningkatkan kompetensi anggota {$ekskul->nama_ekskul}.",
                    'tanggal_kegiatan' => $tanggalKegiatan->toDateString(),
                    'tanggal_berakhir' => $tanggalKegiatan->toDateString(),
                ]);

                foreach ($pendaftarans as $pIdx => $pend) {
                    $randPresensi = ($pend->id + $kIdx) % 10;
                    $statusPresensi = match (true) {
                        $randPresensi < 7 => Presensi::STATUS_HADIR,
                        $randPresensi === 7 => Presensi::STATUS_IZIN,
                        $randPresensi === 8 => Presensi::STATUS_SAKIT,
                        default => Presensi::STATUS_ALPHA,
                    };

                    Presensi::create([
                        'kegiatan_id' => $kegiatan->id,
                        'pendaftaran_id' => $pend->id,
                        'status' => $statusPresensi,
                    ]);
                }
            }
        }

        // ---------------------------------------------------------------------
        // 11. PRESTASI EKSKUL UNTUK DOKUMENTASI
        // ---------------------------------------------------------------------
        $prestasiData = [
            ['Futsal', 'Juara 1 Turnamen Futsal Pelajar Kota Bandung', 'Olahraga', 2026],
            ['Bola Basket', 'Juara 2 DBL Regional Jawa Barat', 'Olahraga', 2026],
            ['Seni Tari', 'Juara 1 Festival Tari Tradisional Nusantara', 'Seni', 2026],
            ['English Club', 'Best Speaker English Debate Championship', 'Bahasa', 2026],
            ['Paskibra', 'Juara Umum LKBB Tingkat Provinsi', 'Lainnya', 2026],
            ['Robotika', 'Medali Perak Indonesian Youth Robotic Olympiad', 'Lainnya', 2026],
        ];

        foreach ($prestasiData as [$namaEks, $judul, $kat, $thn]) {
            $e = Ekskul::where('nama_ekskul', $namaEks)->first();
            if ($e) {
                Prestasi::create([
                    'ekskul_id' => $e->id,
                    'judul' => $judul,
                    'kategori' => $kat,
                    'tahun' => $thn,
                ]);
            }
        }

        $this->command->info('✅ Dummy data berhasil diperbarui dengan sempurna:');
        $this->command->info('   - 36 Kelas (Tingkat 10, 11, 12 untuk 6 Jurusan & 2 Rombel sesuai format kesiswaan)');
        $this->command->info('   - 18 Ekskul lengkap di 4 Kategori (Olahraga, Seni, Bahasa, Lainnya)');
        $this->command->info('   - 6 Akun Pembina dengan NIP 18 Digit');
        $this->command->info('   - 88 Akun Siswa dengan Nama Berbeda, NIS 10 Digit, & Kelas yang Sesuai');
        $this->command->info('   - Total Akun Pengguna: 96 Akun (Termasuk Admin & Kesiswaan)');
    }
}
