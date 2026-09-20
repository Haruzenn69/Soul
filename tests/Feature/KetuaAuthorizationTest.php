<?php

namespace Tests\Feature;

use App\Models\Pendaftaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesFixtures;
use Tests\TestCase;

class KetuaAuthorizationTest extends TestCase
{
    use CreatesFixtures, RefreshDatabase;

    public function test_siswa_biasa_tidak_bisa_akses_area_ketua(): void
    {
        $anggotaUser = $this->makeUser('siswa');
        $this->makeSiswa($anggotaUser, $this->makeKelas('x'), 'anggota');

        $this->actingAs($anggotaUser)
            ->get('/ketua/dashboard')
            ->assertForbidden();
    }

    public function test_pembina_tidak_bisa_akses_area_ketua(): void
    {
        $pembina = $this->makePembina();

        $this->actingAs($pembina->user)
            ->get('/ketua/dashboard')
            ->assertForbidden();
    }

    public function test_ketua_dari_ekskul_tidak_bisa_kelola_pendaftaran_ekskul_lain(): void
    {
        $ekskulA = $this->makeEkskul();
        $ketuaA = $this->makeKetua($ekskulA);

        $ekskulB = $this->makeEkskul();
        $anggotaB = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('xi'));
        $pendaftaranB = $this->daftarkan($anggotaB, $ekskulB, Pendaftaran::STATUS_PENDING);

        $this->actingAs($ketuaA['user'])
            ->patch('/ketua/pendaftaran/'.$pendaftaranB->id, ['status' => Pendaftaran::STATUS_DITERIMA])
            ->assertForbidden();
    }

    public function test_ketua_tidak_bisa_hapus_kegiatan_ekskul_lain(): void
    {
        $ekskulA = $this->makeEkskul();
        $ketuaA = $this->makeKetua($ekskulA);

        $ekskulB = $this->makeEkskul();
        $kegiatanB = $ekskulB->kegiatans()->create([
            'materi' => 'Latihan gabungan',
            'deskripsi' => null,
            'tanggal_kegiatan' => now()->toDateString(),
        ]);

        $this->actingAs($ketuaA['user'])
            ->delete('/ketua/kegiatan/'.$kegiatanB->id)
            ->assertForbidden();
    }
}
