<?php

namespace Tests\Feature;

use App\Models\Ekskul;
use App\Models\Pendaftaran;
use App\Services\PendaftaranDedupService;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\Support\CreatesFixtures;
use Tests\TestCase;

class PendaftaranDedupTest extends TestCase
{
    use CreatesFixtures, RefreshDatabase;

    public function test_unique_index_mencegah_pendaftaran_ganda(): void
    {
        $ekskul = $this->makeEkskul();
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));
        $this->daftarkan($siswa, $ekskul, Pendaftaran::STATUS_DITERIMA);

        $this->expectException(QueryException::class);

        $this->daftarkan($siswa, $ekskul, Pendaftaran::STATUS_DITERIMA);
    }

    public function test_dedup_menggabungkan_dan_memindahkan_relatasi(): void
    {
        $ekskul = $this->makeEkskul();
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));

        // Baris lama (nonaktif) membawa riwayat: presensi, penilaian, notifikasi.
        $lama = $this->daftarkan($siswa, $ekskul, Pendaftaran::STATUS_NONAKTIF);

        $kegiatan = $this->buatKegiatan($ekskul);

        DB::table('presensis')->insert([
            'kegiatan_id' => $kegiatan,
            'pendaftaran_id' => $lama->id,
            'status' => 'hadir',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('penilaians')->insert([
            'pendaftaran_id' => $lama->id,
            'periode' => '2026/2027-1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('notifikasis')->insert([
            'siswa_id' => $siswa->id,
            'pendaftaran_id' => $lama->id,
            'judul' => 'Keanggotaan dinonaktifkan',
            'pesan' => 'Keanggotaanmu dinonaktifkan oleh ketua.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Baris duplikat (diterima) tanpa riwayat — sisa dari bug lama.
        $duplikat = $this->buatDuplikat($siswa->id, $ekskul->id, Pendaftaran::STATUS_DITERIMA);

        (new PendaftaranDedupService())->run();

        $this->assertDatabaseCount('pendaftarans', 1);
        $this->assertDatabaseHas('pendaftarans', ['id' => $lama->id, 'status' => Pendaftaran::STATUS_NONAKTIF]);
        $this->assertDatabaseMissing('pendaftarans', ['id' => $duplikat->id]);
        $this->assertDatabaseHas('presensis', ['pendaftaran_id' => $lama->id, 'kegiatan_id' => $kegiatan]);
        $this->assertDatabaseHas('penilaians', ['pendaftaran_id' => $lama->id, 'periode' => '2026/2027-1']);
        $this->assertDatabaseHas('notifikasis', ['pendaftaran_id' => $lama->id, 'judul' => 'Keanggotaan dinonaktifkan']);
    }

    public function test_dedup_menangani_bentrok_presensi_kegiatan_sama(): void
    {
        $ekskul = $this->makeEkskul();
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));

        // Keduanya sama-sama punya riwayat presensi — pemenang ditentukan
        // presensi terbanyak, lalu tanggal daftar, lalu id terkecil.
        $utama = $this->daftarkan($siswa, $ekskul, Pendaftaran::STATUS_NONAKTIF);
        $duplikat = $this->buatDuplikat($siswa->id, $ekskul->id, Pendaftaran::STATUS_DITERIMA);

        $kegiatan1 = $this->buatKegiatan($ekskul);
        $kegiatan2 = $this->buatKegiatan($ekskul);
        $kegiatan3 = $this->buatKegiatan($ekskul);

        $this->catatPresensi($utama->id, $kegiatan1);
        $this->catatPresensi($utama->id, $kegiatan2);
        $this->catatPresensi($duplikat->id, $kegiatan1);
        $this->catatPresensi($duplikat->id, $kegiatan3);

        (new PendaftaranDedupService())->run();

        $this->assertDatabaseCount('pendaftarans', 1);
        $this->assertDatabaseHas('pendaftarans', ['id' => $utama->id, 'status' => Pendaftaran::STATUS_NONAKTIF]);
        $this->assertDatabaseMissing('pendaftarans', ['id' => $duplikat->id]);

        // Kegiatan bentrok (kegiatan1) hanya menyisakan satu baris, milik $utama.
        $this->assertSame(1, DB::table('presensis')->where('kegiatan_id', $kegiatan1)->count());
        $this->assertDatabaseHas('presensis', ['kegiatan_id' => $kegiatan1, 'pendaftaran_id' => $utama->id]);
        // Kegiatan non-bentrok tetap ada dan menempel ke baris utama.
        $this->assertDatabaseHas('presensis', ['kegiatan_id' => $kegiatan2, 'pendaftaran_id' => $utama->id]);
        $this->assertDatabaseHas('presensis', ['kegiatan_id' => $kegiatan3, 'pendaftaran_id' => $utama->id]);
    }

    private function buatDuplikat(int $siswaId, int $ekskulId, string $status): Pendaftaran
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->dropUnique('pendaftarans_siswa_ekskul_unique');
        });

        return Pendaftaran::create([
            'siswa_id' => $siswaId,
            'ekskul_id' => $ekskulId,
            'tanggal_daftar' => now()->toDateString(),
            'status' => $status,
            'alasan' => 'Saya ingin mengembangkan keterampilan dan pengalaman.',
        ]);
    }

    private function buatKegiatan(Ekskul $ekskul): int
    {
        return DB::table('kegiatans')->insertGetId([
            'ekskul_id' => $ekskul->id,
            'materi' => 'Latihan '.fake()->word(),
            'tanggal_kegiatan' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function catatPresensi(int $pendaftaranId, int $kegiatanId): void
    {
        DB::table('presensis')->insert([
            'kegiatan_id' => $kegiatanId,
            'pendaftaran_id' => $pendaftaranId,
            'status' => 'hadir',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
