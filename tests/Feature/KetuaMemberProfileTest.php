<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesFixtures;
use Tests\TestCase;

class KetuaMemberProfileTest extends TestCase
{
    use CreatesFixtures, RefreshDatabase;

    public function test_ketua_dapat_membuka_profil_anggota_di_ekskulnya(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);
        $anggota = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('xi'));
        $pendaftaran = $this->daftarkan($anggota, $ekskul);

        $this->actingAs($ketua['user'])
            ->get(route('ketua.anggota.index'))
            ->assertOk()
            ->assertSee(route('ketua.anggota.show', $pendaftaran))
            ->assertSee('Aksi')
            ->assertSee('Peringatan')
            ->assertSee('disabled', false)
            ->assertDontSee('Kehadiran');

        $this->actingAs($ketua['user'])
            ->get(route('ketua.anggota.show', $pendaftaran))
            ->assertOk()
            ->assertSee($anggota->nama)
            ->assertSee('Profil Lengkap')
            ->assertSee(route('ketua.anggota.index', ['ekskul' => $ekskul->id]));
    }

    public function test_ketua_tidak_dapat_membuka_profil_anggota_ekskul_lain(): void
    {
        $ekskulKetua = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskulKetua);
        $ekskulLain = $this->makeEkskul();
        $anggota = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('xi'));
        $pendaftaran = $this->daftarkan($anggota, $ekskulLain);

        $this->actingAs($ketua['user'])
            ->get(route('ketua.anggota.show', $pendaftaran))
            ->assertForbidden();
    }
}
