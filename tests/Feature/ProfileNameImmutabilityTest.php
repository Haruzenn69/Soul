<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesFixtures;
use Tests\TestCase;

class ProfileNameImmutabilityTest extends TestCase
{
    use CreatesFixtures, RefreshDatabase;

    public function test_siswa_tidak_bisa_mengubah_nama_via_form_profil(): void
    {
        $kelas = $this->makeKelas('x');
        $user = $this->makeUser('siswa');
        $siswa = $this->makeSiswa($user, $kelas);

        $response = $this->actingAs($user)->post(route('siswa.profile.update-data'), [
            'nama' => 'NAMA PALSU',
            'kelas_id' => $kelas->id,
            'jenis_kelamin' => 'perempuan',
            'email' => null,
        ]);

        $response->assertRedirect(route('siswa.profile.edit'));

        $this->assertSame($siswa->fresh()->nama, $siswa->nama);
        $this->assertNotSame($siswa->fresh()->nama, 'NAMA PALSU');
        $this->assertSame($siswa->fresh()->jenis_kelamin, 'perempuan');
    }

    public function test_pembina_tidak_bisa_mengubah_nama_via_form_profil(): void
    {
        $pembina = $this->makePembina();

        $response = $this->actingAs($pembina->user)->post(route('pembina.profile.update'), [
            'nama' => 'NAMA PALSU',
            'jenis_kelamin' => 'perempuan',
            'email' => null,
        ]);

        $response->assertRedirect(route('pembina.profile'));

        $this->assertSame($pembina->fresh()->nama, $pembina->nama);
        $this->assertNotSame($pembina->fresh()->nama, 'NAMA PALSU');
        $this->assertSame($pembina->fresh()->jenis_kelamin, 'perempuan');
    }
}