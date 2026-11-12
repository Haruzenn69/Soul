<?php

namespace Tests\Feature;

use App\Models\Pendaftaran;
use App\Models\RiwayatPendaftaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesFixtures;
use Tests\TestCase;

class PendaftaranFlowTest extends TestCase
{
    use CreatesFixtures, RefreshDatabase;

    public function test_siswa_tidak_dapat_mendaftar_sendiri_melalui_web_atau_api(): void
    {
        $this->post('/siswa/daftar-ekskul', ['ekskul_id' => 1])->assertNotFound();
        $this->postJson('/api/siswa/daftar-ekskul', ['ekskul_id' => 1])->assertNotFound();
        $this->getJson('/api/siswa/daftar-exkul')->assertNotFound();
    }

    public function test_tombol_tambah_anggota_hanya_ada_di_halaman_pendaftaran(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);

        $this->actingAs($ketua['user'])
            ->get(route('ketua.pendaftaran.index'))
            ->assertOk()
            ->assertSee('Tambah Anggota')
            ->assertSee('modal-tambah-anggota');

        $this->get(route('ketua.anggota.index'))
            ->assertOk()
            ->assertDontSee('Tambah Anggota')
            ->assertDontSee('modal-tambah-anggota');
    }

    public function test_ketua_dapat_menambahkan_siswa_dan_notifikasi_dikirim(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('xi'));

        $this->actingAs($ketua['user'])
            ->from(route('ketua.pendaftaran.index'))
            ->post(route('ketua.anggota.store'), ['siswa_ids' => [$siswa->id]])
            ->assertRedirect(route('ketua.pendaftaran.index'))
            ->assertSessionHas('success');

        $pendaftaran = Pendaftaran::where('siswa_id', $siswa->id)->where('ekskul_id', $ekskul->id)->firstOrFail();
        $this->assertSame(Pendaftaran::STATUS_DITERIMA, $pendaftaran->status);
        $this->assertSame('anggota', $siswa->fresh()->jabatan);
        $this->assertDatabaseHas('notifikasis', [
            'siswa_id' => $siswa->id,
            'pendaftaran_id' => $pendaftaran->id,
            'judul' => 'Ditambahkan ke Ekskul',
        ]);
    }

    public function test_ketua_tidak_dapat_menambahkan_siswa_melebihi_batas_ekskul(): void
    {
        $ekskulA = $this->makeEkskul(['nama_ekskul' => 'Basket']);
        $ekskulB = $this->makeEkskul(['nama_ekskul' => 'Futsal']);
        $target = $this->makeEkskul(['nama_ekskul' => 'Tari']);
        $ketua = $this->makeKetua($target);
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));
        $this->daftarkan($siswa, $ekskulA);
        $this->daftarkan($siswa, $ekskulB);

        $this->actingAs($ketua['user'])
            ->from(route('ketua.pendaftaran.index'))
            ->post(route('ketua.anggota.store'), ['siswa_ids' => [$siswa->id]])
            ->assertRedirect(route('ketua.pendaftaran.index'))
            ->assertSessionHasErrors('siswa_ids');

        $this->assertDatabaseMissing('pendaftarans', [
            'siswa_id' => $siswa->id,
            'ekskul_id' => $target->id,
        ]);
    }

    public function test_ketua_tidak_dapat_menambahkan_siswa_ke_ekskul_wajib_yang_bentrok(): void
    {
        $wajibAktif = $this->makeEkskul(['nama_ekskul' => 'Pramuka Inti', 'kategori' => 'Wajib']);
        $wajibTarget = $this->makeEkskul(['nama_ekskul' => 'PMR Utama', 'kategori' => 'Wajib']);
        $ketua = $this->makeKetua($wajibTarget);
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));
        $this->daftarkan($siswa, $wajibAktif);

        $this->actingAs($ketua['user'])
            ->from(route('ketua.pendaftaran.index'))
            ->post(route('ketua.anggota.store'), ['siswa_ids' => [$siswa->id]])
            ->assertRedirect(route('ketua.pendaftaran.index'))
            ->assertSessionHasErrors('siswa_ids');

        $this->assertDatabaseMissing('pendaftarans', [
            'siswa_id' => $siswa->id,
            'ekskul_id' => $wajibTarget->id,
        ]);
    }

    public function test_ketua_tidak_dapat_menambahkan_siswa_ke_ekskul_4p1i_yang_bentrok(): void
    {
        $paskibra = $this->makeEkskul(['nama_ekskul' => 'Paskibra']);
        $polsis = $this->makeEkskul(['nama_ekskul' => 'Polsis']);
        $ketua = $this->makeKetua($polsis);
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));
        $this->daftarkan($siswa, $paskibra);

        $this->actingAs($ketua['user'])
            ->from(route('ketua.pendaftaran.index'))
            ->post(route('ketua.anggota.store'), ['siswa_ids' => [$siswa->id]])
            ->assertRedirect(route('ketua.pendaftaran.index'))
            ->assertSessionHasErrors('siswa_ids');

        $this->assertDatabaseMissing('pendaftarans', [
            'siswa_id' => $siswa->id,
            'ekskul_id' => $polsis->id,
        ]);
    }

    public function test_siswa_biasa_tidak_dapat_menambahkan_anggota(): void
    {
        $ekskul = $this->makeEkskul();
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));

        $this->actingAs($siswa->user)
            ->post(route('ketua.anggota.store'), ['siswa_ids' => [$siswa->id]])
            ->assertForbidden();

        $this->assertDatabaseMissing('pendaftarans', [
            'siswa_id' => $siswa->id,
            'ekskul_id' => $ekskul->id,
        ]);
    }

    public function test_ketua_dapat_mengaktifkan_kembali_siswa_yang_pernah_keluar(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('xi'));
        $existing = $this->daftarkan($siswa, $ekskul, Pendaftaran::STATUS_KELUAR);

        $this->actingAs($ketua['user'])
            ->post(route('ketua.anggota.store'), ['siswa_ids' => [$siswa->id]])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(Pendaftaran::STATUS_DITERIMA, $existing->fresh()->status);
        $riwayat = RiwayatPendaftaran::where('pendaftaran_id', $existing->id)->firstOrFail();
        $this->assertSame(Pendaftaran::STATUS_KELUAR, $riwayat->status);
        $this->assertSame($existing->tanggal_daftar->toDateString(), $riwayat->tanggal_daftar->toDateString());
    }

    public function test_ketua_dapat_menonaktifkan_anggota_yang_juga_menjabat_sebagai_ketua(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);
        $anggotaKetua = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('xi'), 'ketua');
        $pendaftaran = $this->daftarkan($anggotaKetua, $ekskul);

        $this->actingAs($ketua['user'])
            ->from(route('ketua.anggota.index'))
            ->patch(route('ketua.anggota.update-status', $pendaftaran), ['status' => Pendaftaran::STATUS_NONAKTIF])
            ->assertRedirect(route('ketua.anggota.index'))
            ->assertSessionHas('success');

        $this->assertSame(Pendaftaran::STATUS_NONAKTIF, $pendaftaran->fresh()->status);
    }

    public function test_ketua_tidak_dapat_menonaktifkan_keanggotaan_dirinya_sendiri(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);
        $pendaftaran = Pendaftaran::where('siswa_id', $ketua['siswa']->id)
            ->where('ekskul_id', $ekskul->id)
            ->firstOrFail();

        $this->actingAs($ketua['user'])
            ->from(route('ketua.anggota.index'))
            ->patch(route('ketua.anggota.update-status', $pendaftaran), ['status' => Pendaftaran::STATUS_NONAKTIF])
            ->assertRedirect(route('ketua.anggota.index'))
            ->assertSessionHas('error', 'Kamu tidak bisa memberi peringatan atau menonaktifkan dirimu sendiri.');

        $this->assertSame(Pendaftaran::STATUS_DITERIMA, $pendaftaran->fresh()->status);
    }

    public function test_pencarian_calon_siswa_mendukung_kata_kunci_dan_paginasi(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);
        $targetSiswa = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('xi'));
        $targetSiswa->update(['nis' => '1234567890']);

        for ($i = 0; $i < 26; $i++) {
            $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));
        }

        $this->actingAs($ketua['user'])
            ->getJson(route('ketua.anggota.calon-siswa'))
            ->assertOk()
            ->assertJsonPath('total', 28)
            ->assertJsonPath('last_page', 2);

        $this->actingAs($ketua['user'])
            ->getJson(route('ketua.anggota.calon-siswa', ['q' => '1234567890']))
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('items.0.id', $targetSiswa->id);

        $this->actingAs($ketua['user'])
            ->getJson(route('ketua.anggota.calon-siswa', ['kelas_id' => $targetSiswa->kelas_id]))
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('items.0.id', $targetSiswa->id);
    }

    public function test_calon_siswa_yang_sudah_aktif_tetap_muncul_di_urutan_terakhir_dan_ditandai(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);
        $calon = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('xi'));
        $anggotaAktif = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('xi'));
        $this->daftarkan($anggotaAktif, $ekskul, Pendaftaran::STATUS_DITERIMA);

        $this->actingAs($ketua['user'])
            ->getJson(route('ketua.anggota.calon-siswa'))
            ->assertOk()
            ->assertJsonPath('total', 3)
            ->assertJsonPath('items.1.sudah_aktif', true)
            ->assertJsonPath('items.2.sudah_aktif', true)
            ->assertJsonPath('items.0.id', $calon->id)
            ->assertJsonPath('items.0.sudah_aktif', false);
    }
}
