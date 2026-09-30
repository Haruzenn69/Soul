<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesFixtures;
use Tests\TestCase;

class UserCreateFormTest extends TestCase
{
    use CreatesFixtures, RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function payloadSiswa(array $overrides = []): array
    {
        return array_merge([
            'role' => 'siswa',
            'nama' => 'Nisa Ramadan',
            'nis' => '3000000001',
            'email' => 'nisa.ramadan@example.test',
            'kelas_id' => $this->makeKelas('x')->id,
            'jabatan' => 'siswa',
            'jenis_kelamin' => 'perempuan',
            'agama' => 'Islam',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2008-05-14',
        ], $overrides);
    }

    public function test_form_buat_akun_tidak_menampilkan_input_angkatan(): void
    {
        $this->makeKelas('x');

        $response = $this->actingAs($this->makeUser('kesiswaan'))
            ->get(route('kesiswaan.users.create'));

        $response->assertOk();
        $response->assertDontSee('name="angkatan"', false);
        $response->assertDontSee('Tahun Angkatan');
    }

    public function test_form_buat_akun_tetap_menampilkan_pemilihan_kelas(): void
    {
        $kelas = $this->makeKelas('x');

        $response = $this->actingAs($this->makeUser('kesiswaan'))
            ->get(route('kesiswaan.users.create'));

        $response->assertOk();
        $response->assertSee('name="kelas_id"', false);
        $response->assertSee($kelas->nama);
    }

    public function test_form_buat_akun_menandai_biodata_wajib_dan_tanpa_keterangan_opsional(): void
    {
        $this->makeKelas('x');

        $response = $this->actingAs($this->makeUser('kesiswaan'))
            ->get(route('kesiswaan.users.create'));

        $response->assertOk();

        foreach (['Agama', 'Tempat Lahir', 'Tanggal Lahir'] as $label) {
            $response->assertSee($label.' <span class="text-rose-500">*</span>', false);
            $response->assertDontSee($label.' <span class="text-slate-400 font-normal">(Opsional)</span>', false);
        }
    }

    public function test_penyimpanan_akun_tanpa_angkatan_tetap_berhasil(): void
    {
        $response = $this->actingAs($this->makeUser('kesiswaan'))
            ->post(route('kesiswaan.users.store'), $this->payloadSiswa());

        $response->assertRedirect(route('kesiswaan.users.index'));
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('siswas', [
            'nis' => '3000000001',
            'nama' => 'Nisa Ramadan',
            'angkatan' => null,
            'agama' => 'Islam',
            'tempat_lahir' => 'Bandung',
        ]);
    }

    public function test_penyimpanan_akun_gagal_tanpa_agama(): void
    {
        $response = $this->actingAs($this->makeUser('kesiswaan'))
            ->post(route('kesiswaan.users.store'), $this->payloadSiswa(['agama' => '']));

        $response->assertSessionHasErrors('agama');
        $this->assertDatabaseMissing('siswas', ['nis' => '3000000001']);
    }

    public function test_penyimpanan_akun_gagal_tanpa_tempat_lahir(): void
    {
        $response = $this->actingAs($this->makeUser('kesiswaan'))
            ->post(route('kesiswaan.users.store'), $this->payloadSiswa(['tempat_lahir' => '']));

        $response->assertSessionHasErrors('tempat_lahir');
        $this->assertDatabaseMissing('siswas', ['nis' => '3000000001']);
    }

    public function test_penyimpanan_akun_gagal_tanpa_tanggal_lahir(): void
    {
        $response = $this->actingAs($this->makeUser('kesiswaan'))
            ->post(route('kesiswaan.users.store'), $this->payloadSiswa(['tanggal_lahir' => '']));

        $response->assertSessionHasErrors('tanggal_lahir');
        $this->assertDatabaseMissing('siswas', ['nis' => '3000000001']);
    }
}
