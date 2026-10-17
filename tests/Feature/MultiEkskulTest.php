<?php

namespace Tests\Feature;

use App\Models\Pendaftaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesFixtures;
use Tests\TestCase;

class MultiEkskulTest extends TestCase
{
    use CreatesFixtures, RefreshDatabase;

    private const ALASAN = 'Saya ingin mengembangkan keterampilan dan pengalaman.';

    private function daftarStore($siswaUser, int $ekskulId)
    {
        return $this->actingAs($siswaUser)->post(route('siswa.daftar-ekskul.store'), [
            'ekskul_id' => $ekskulId,
            'alasan' => self::ALASAN,
        ]);
    }

    public function test_siswa_bisa_mendaftar_ekskul_kedua(): void
    {
        $ekskulA = $this->makeEkskul(['nama_ekskul' => 'Basket']);
        $ekskulB = $this->makeEkskul(['nama_ekskul' => 'Futsal']);
        $siswaUser = $this->makeUser('siswa');
        $siswa = $this->makeSiswa($siswaUser, $this->makeKelas('xii'));
        $this->daftarkan($siswa, $ekskulA);

        $this->daftarStore($siswaUser, $ekskulB->id)
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('pendaftarans', [
            'siswa_id' => $siswa->id,
            'ekskul_id' => $ekskulB->id,
            'status' => Pendaftaran::STATUS_PENDING,
        ]);
    }

    public function test_siswa_tidak_bisa_mendaftar_ekskul_ketiga(): void
    {
        $ekskulA = $this->makeEkskul(['nama_ekskul' => 'Basket']);
        $ekskulB = $this->makeEkskul(['nama_ekskul' => 'Futsal']);
        $ekskulC = $this->makeEkskul(['nama_ekskul' => 'Tari']);
        $siswaUser = $this->makeUser('siswa');
        $siswa = $this->makeSiswa($siswaUser, $this->makeKelas('xii'));
        $this->daftarkan($siswa, $ekskulA);
        $this->daftarkan($siswa, $ekskulB);

        $this->daftarStore($siswaUser, $ekskulC->id)
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('pendaftarans', [
            'siswa_id' => $siswa->id,
            'ekskul_id' => $ekskulC->id,
        ]);
    }

    public function test_pendaftaran_menunggu_ikut_menghitung_batas_dua_ekskul(): void
    {
        $ekskulA = $this->makeEkskul(['nama_ekskul' => 'Basket']);
        $ekskulB = $this->makeEkskul(['nama_ekskul' => 'Futsal']);
        $ekskulC = $this->makeEkskul(['nama_ekskul' => 'Tari']);
        $siswaUser = $this->makeUser('siswa');
        $siswa = $this->makeSiswa($siswaUser, $this->makeKelas('xii'));
        $this->daftarkan($siswa, $ekskulA);
        $this->daftarkan($siswa, $ekskulB, Pendaftaran::STATUS_PENDING);

        $this->daftarStore($siswaUser, $ekskulC->id)
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    public function test_siswa_tidak_bisa_mendaftar_dua_ekskul_4p1i(): void
    {
        $paskibra = $this->makeEkskul(['nama_ekskul' => 'Paskibra']);
        $polsis = $this->makeEkskul(['nama_ekskul' => 'Polsis']);
        $siswaUser = $this->makeUser('siswa');
        $siswa = $this->makeSiswa($siswaUser, $this->makeKelas('xii'));
        $this->daftarkan($siswa, $paskibra);

        $this->daftarStore($siswaUser, $polsis->id)
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('pendaftarans', [
            'siswa_id' => $siswa->id,
            'ekskul_id' => $polsis->id,
        ]);
    }

    public function test_siswa_bisa_mendaftar_non_4p1i_saat_sudah_punya_satu_4p1i(): void
    {
        $paskibra = $this->makeEkskul(['nama_ekskul' => 'Paskibra']);
        $basket = $this->makeEkskul(['nama_ekskul' => 'Basket']);
        $siswaUser = $this->makeUser('siswa');
        $siswa = $this->makeSiswa($siswaUser, $this->makeKelas('xii'));
        $this->daftarkan($siswa, $paskibra);

        $this->daftarStore($siswaUser, $basket->id)
            ->assertRedirect()
            ->assertSessionHas('success');
    }

    public function test_katalog_menandai_ekskul_yang_sudah_diikuti(): void
    {
        $ekskulA = $this->makeEkskul(['nama_ekskul' => 'Basket']);
        $ekskulB = $this->makeEkskul(['nama_ekskul' => 'Futsal']);
        $siswaUser = $this->makeUser('siswa');
        $siswa = $this->makeSiswa($siswaUser, $this->makeKelas('xii'));
        $this->daftarkan($siswa, $ekskulA);
        $this->daftarkan($siswa, $ekskulB);

        $this->actingAs($siswaUser)
            ->get(route('siswa.katalog'))
            ->assertOk()
            ->assertSee('Ekskul Kamu');
    }

    public function test_siswa_bisa_melihat_switcher_ekskul_di_halaman_presensi(): void
    {
        $ekskulA = $this->makeEkskul(['nama_ekskul' => 'Basket']);
        $ekskulB = $this->makeEkskul(['nama_ekskul' => 'Futsal']);
        $siswaUser = $this->makeUser('siswa');
        $siswa = $this->makeSiswa($siswaUser, $this->makeKelas('xii'));
        $this->daftarkan($siswa, $ekskulA);
        $this->daftarkan($siswa, $ekskulB);

        $this->actingAs($siswaUser)
            ->get(route('siswa.presensi'))
            ->assertOk()
            ->assertSee('Pilih Ekskul')
            ->assertSee('Basket')
            ->assertSee('Futsal');

        $this->actingAs($siswaUser)
            ->get(route('siswa.presensi', ['ekskul' => $ekskulB->id]))
            ->assertOk()
            ->assertSee('Futsal');
    }

    public function test_siswa_bisa_melihat_switcher_ekskul_di_halaman_nilai(): void
    {
        $ekskulA = $this->makeEkskul(['nama_ekskul' => 'Basket']);
        $ekskulB = $this->makeEkskul(['nama_ekskul' => 'Futsal']);
        $siswaUser = $this->makeUser('siswa');
        $siswa = $this->makeSiswa($siswaUser, $this->makeKelas('xii'));
        $this->daftarkan($siswa, $ekskulA);
        $this->daftarkan($siswa, $ekskulB);

        $this->actingAs($siswaUser)
            ->get(route('siswa.nilai'))
            ->assertOk()
            ->assertSee('Pilih Ekskul')
            ->assertSee('Basket')
            ->assertSee('Futsal');
    }

    public function test_siswa_bisa_memilih_ekskul_saat_mengajukan_keluar(): void
    {
        $ekskulA = $this->makeEkskul(['nama_ekskul' => 'Basket']);
        $ekskulB = $this->makeEkskul(['nama_ekskul' => 'Futsal']);
        $siswaUser = $this->makeUser('siswa');
        $siswa = $this->makeSiswa($siswaUser, $this->makeKelas('xii'));
        $this->daftarkan($siswa, $ekskulA);
        $this->daftarkan($siswa, $ekskulB);

        $this->actingAs($siswaUser)
            ->get(route('siswa.pengajuan-keluar', ['ekskul' => $ekskulB->id]))
            ->assertOk()
            ->assertSee('Pilih Ekskul')
            ->assertSee('Mengajukan keluar dari')
            ->assertSee('Futsal');

        $this->actingAs($siswaUser)
            ->post(route('siswa.pengajuan-keluar.store'), [
                'ekskul_id' => $ekskulB->id,
                'alasan' => 'Saya ingin lebih fokus pada kegiatan akademik semester ini.',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('pengajuan_keluars', [
            'siswa_id' => $siswa->id,
            'ekskul_id' => $ekskulB->id,
            'status' => 'pending',
        ]);
        $this->assertDatabaseMissing('pengajuan_keluars', [
            'siswa_id' => $siswa->id,
            'ekskul_id' => $ekskulA->id,
        ]);
    }

    public function test_dashboard_siswa_menampilkan_semua_ekskul_aktif(): void
    {
        $ekskulA = $this->makeEkskul(['nama_ekskul' => 'Basket']);
        $ekskulB = $this->makeEkskul(['nama_ekskul' => 'Futsal']);
        $siswaUser = $this->makeUser('siswa');
        $siswa = $this->makeSiswa($siswaUser, $this->makeKelas('xii'));
        $this->daftarkan($siswa, $ekskulA);
        $this->daftarkan($siswa, $ekskulB);

        $this->actingAs($siswaUser)
            ->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertSee('Aktif di Basket')
            ->assertSee('Aktif di Futsal')
            ->assertSee('2');
    }

    public function test_ketua_tetap_mengelola_ekskul_yang_dipimpin_meskipun_ikut_ekskul_lain(): void
    {
        $ekskulPimpin = $this->makeEkskul(['nama_ekskul' => 'Paskibra']);
        $ekskulAnggota = $this->makeEkskul(['nama_ekskul' => 'Basket']);

        $ketuaUser = $this->makeUser('siswa');
        $siswa = $this->makeSiswa($ketuaUser, $this->makeKelas('xii'), 'ketua');
        $this->daftarkan($siswa, $ekskulPimpin);
        $this->daftarkan($siswa, $ekskulAnggota);

        app(\App\Services\ArsipEkskulService::class)->mulaiPeriodeKetua($ekskulPimpin, $siswa);

        $this->assertSame($ekskulPimpin->id, $siswa->fresh()->ekskulDipimpin()?->id);
        $this->assertSame($ekskulPimpin->id, $ekskulPimpin->fresh()->ketua()?->id);
        $this->assertNull($ekskulAnggota->fresh()->ketua());

        $this->actingAs($ketuaUser)
            ->get(route('ketua.dashboard'))
            ->assertOk()
            ->assertSee('Paskibra');
    }

    public function test_pembina_tidak_bisa_mengangkat_ketua_yang_sudah_menjabat_ketua_di_ekskul_lain(): void
    {
        $pembinaUser = $this->makeUser('pembina');
        $pembina = \App\Models\Pembina::create([
            'nip' => 'NIP99998888',
            'user_id' => $pembinaUser->id,
            'nama' => 'Pak Pembina',
            'jenis_kelamin' => 'laki-laki',
        ]);
        $ekskulPimpin = $this->makeEkskul(['nama_ekskul' => 'Paskibra']);
        $ekskulLain = $this->makeEkskul(['nama_ekskul' => 'Basket', 'pembina_id' => $pembina->id]);

        $ketuaUser = $this->makeUser('siswa');
        $siswa = $this->makeSiswa($ketuaUser, $this->makeKelas('xii'), 'ketua');
        $this->daftarkan($siswa, $ekskulPimpin);
        $this->daftarkan($siswa, $ekskulLain);

        app(\App\Services\ArsipEkskulService::class)->mulaiPeriodeKetua($ekskulPimpin, $siswa);

        $this->actingAs($pembinaUser)
            ->post(route('pembina.ekskul.pilih-ketua', ['ekskul' => $ekskulLain->id, 'siswa' => $siswa->id]))
            ->assertRedirect()
            ->assertSessionHas('error');
    }
}
