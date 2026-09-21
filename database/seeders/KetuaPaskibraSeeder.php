<?php

namespace Database\Seeders;

use App\Models\Ekskul;
use App\Models\Kelas;
use App\Models\Pembina;
use App\Models\Pendaftaran;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KetuaPaskibraSeeder extends Seeder
{
    public function run(): void
    {
        // ===== PEMBINA PASKIBRA (idempotent) =====
        $userPembinaPaskibra = User::firstOrCreate(
            ['email' => 'pembina_paskibra@soul.test'],
            [
                'username' => 'pembina_paskibra',
                'password' => Hash::make('password'),
                'role' => 'pembina',
            ]
        );

        $pembinaPaskibra = Pembina::firstOrCreate(
            ['user_id' => $userPembinaPaskibra->id],
            [
                'nip' => '198502022020022002',
                'nama' => 'Bu Siti, S.Pd',
                'jenis_kelamin' => 'perempuan',
            ]
        );

        // ===== EKSKUL PASKIBRA (idempotent) =====
        $ekskulPaskibra = Ekskul::firstOrCreate(
            ['nama_ekskul' => 'Paskibra'],
            [
                'pembina_id' => $pembinaPaskibra->id,
                'pelatih_id' => null,
                'tagline' => 'Berkibar Tinggi, Berkarakter Kuat.',
                'deskripsi' => 'Pasukan Pengibar Bendera - Ekskul prestisius di sekolah',
                'tujuan' => 'Melatih kedisiplinan, kepemimpinan, dan rasa cinta tanah air melalui latihan baris-berbaris dan upacara.',
                'jadwal' => 'Selasa & Jumat, 15:30 - 17:30',
                'is_open_recruitment' => true,
            ]
        );

        // Pastikan ekskul tertaut ke pembina (jika sudah ada tapi pembina_id null)
        if ($ekskulPaskibra->pembina_id !== $pembinaPaskibra->id) {
            $ekskulPaskibra->update(['pembina_id' => $pembinaPaskibra->id]);
        }

        // ===== KELAS XII RPL 1 - nama "12 RPL 1" (ambil dari seeder existing) =====
        $kelas = Kelas::where('nama', '12 RPL 1')->first();
        if (! $kelas) {
            $tahunAjaran = TahunAjaran::where('is_active', true)->first();
            if (! $tahunAjaran) {
                $tahunAjaran = TahunAjaran::create([
                    'nama' => '2026/2027',
                    'is_active' => true,
                ]);
            }
            $kelas = Kelas::create([
                'nama' => '12 RPL 1',
                'tingkat' => 'xii',
                'tahun_ajaran_id' => $tahunAjaran->id,
            ]);
        }

        // ===== AKUN KETUA PASKIBRA BARU =====
        $userKetua = User::firstOrCreate(
            ['email' => 'ketua_paskibra2@soul.test'],
            [
                'username' => 'ketua_paskibra2',
                'password' => Hash::make('password'),
                'role' => 'siswa',
            ]
        );

        $siswaKetua = Siswa::firstOrCreate(
            ['user_id' => $userKetua->id],
            [
                'nis' => '2406510099',
                'nama' => 'Fajar Ramadhan',
                'kelas_id' => $kelas->id,
                'jenis_kelamin' => 'laki-laki',
                'jabatan' => 'ketua',
            ]
        );

        // ===== PENDAFTARAN DITERIMA DI PASKIBRA =====
        $pendaftaran = Pendaftaran::firstOrCreate(
            ['siswa_id' => $siswaKetua->id, 'ekskul_id' => $ekskulPaskibra->id],
            [
                'tanggal_daftar' => now()->subDays(1)->toDateString(),
                'status' => 'diterima',
                'alasan' => 'Ingin memimpin ekskul Paskibra dengan dedikasi dan disiplin.',
            ]
        );

        $this->command->info('✅ Akun Ketua Paskibra berhasil dibuat/ditemukan!');
        $this->command->info('');
        $this->command->info('📧 LOGIN KETUA PASKIBRA:');
        $this->command->info('   Email    : ketua_paskibra2@soul.test');
        $this->command->info('   Username : ketua_paskibra2');
        $this->command->info('   Password : password');
        $this->command->info('');
        $this->command->info('📋 Detail Siswa:');
        $this->command->info('   Nama     : Fajar Ramadhan');
        $this->command->info('   NIS      : 2406510099');
        $this->command->info('   Kelas    : '.$kelas->nama);
        $this->command->info('   Jabatan  : Ketua');
        $this->command->info('');
        $this->command->info('🔗 Koneksi data:');
        $this->command->info('   - Ekskul Paskibra (id: '.$ekskulPaskibra->id.') terhubung ke Pembina: '.$pembinaPaskibra->nama);
        $this->command->info('   - Ketua terdaftar di Pendaftaran (id: '.$pendaftaran->id.') dengan status: '.$pendaftaran->status);
        $this->command->info('');
        $this->command->info('💡 Bisa login sekarang untuk mengkonfirmasi pendaftaran via dashboard ketua.');
    }
}
