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

    public function test_siswa_bisa_mendaftar_satu_ekskul_wajib(): void
    {
        $wajib = $this->makeEkskul(['nama_ekskul' => 'Pramuka Inti', 'kategori' => 'Wajib']);
        $siswaUser = $this->makeUser('siswa');
        $siswa = $this->makeSiswa($siswaUser, $this->makeKelas('x'));

        $this->daftarStore($siswaUser, $wajib->id)
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('pendaftarans', [
            'siswa_id' => $siswa->id,
            'ekskul_id' => $wajib->id,
            'status' => Pendaftaran::STATUS_PENDING,
        ]);
    }

    public function test_siswa_tidak_bisa_mendaftar_dua_ekskul_kategori_wajib(): void
    {
        $wajibA = $this->makeEkskul(['nama_ekskul' => 'Pramuka Inti', 'kategori' => 'Wajib']);
        $wajibB = $this->makeEkskul(['nama_ekskul' => 'PMR Utama', 'kategori' => 'Wajib']);
        $siswaUser = $this->makeUser('siswa');
        $siswa = $this->makeSiswa($siswaUser, $this->makeKelas('x'));
        $this->daftarkan($siswa, $wajibA);

        $this->daftarStore($siswaUser, $wajibB->id)
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('pendaftarans', [
            'siswa_id' => $siswa->id,
            'ekskul_id' => $wajibB->id,
        ]);
    }

    public function test_siswa_yang_sudah_ikut_ekskul_wajib_masih_bisa_mendaftar_ekskul_kategori_lain(): void
    {
        $wajib = $this->makeEkskul(['nama_ekskul' => 'Pramuka Inti', 'kategori' => 'Wajib']);
        $futsal = $this->makeEkskul(['nama_ekskul' => 'Futsal', 'kategori' => 'Olahraga']);
        $siswaUser = $this->makeUser('siswa');
        $siswa = $this->makeSiswa($siswaUser, $this->makeKelas('x'));
        $this->daftarkan($siswa, $wajib);

        $this->daftarStore($siswaUser, $futsal->id)
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('pendaftarans', [
            'siswa_id' => $siswa->id,
            'ekskul_id' => $futsal->id,
            'status' => Pendaftaran::STATUS_PENDING,
        ]);
    }

    public function test_siswa_yang_sudah_ikut_ekskul_kategori_lain_masih_bisa_mendaftar_ekskul_wajib(): void
    {
        $futsal = $this->makeEkskul(['nama_ekskul' => 'Futsal', 'kategori' => 'Olahraga']);
        $wajib = $this->makeEkskul(['nama_ekskul' => 'Pramuka Inti', 'kategori' => 'Wajib']);
        $siswaUser = $this->makeUser('siswa');
        $siswa = $this->makeSiswa($siswaUser, $this->makeKelas('x'));
        $this->daftarkan($siswa, $futsal);

        $this->daftarStore($siswaUser, $wajib->id)
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('pendaftarans', [
            'siswa_id' => $siswa->id,
            'ekskul_id' => $wajib->id,
            'status' => Pendaftaran::STATUS_PENDING,
        ]);
    }

    public function test_ketua_tidak_bisa_menerima_siswa_yang_sudah_aktif_di_ekskul_wajib_lain(): void
    {
        $wajibA = $this->makeEkskul(['nama_ekskul' => 'Pramuka Inti', 'kategori' => 'Wajib']);
        $wajibB = $this->makeEkskul(['nama_ekskul' => 'PMR Utama', 'kategori' => 'Wajib']);

        $ketuaFixture = $this->makeKetua($wajibB);
        $ketuaUser = $ketuaFixture['user'];

        $siswaUser = $this->makeUser('siswa');
        $siswa = $this->makeSiswa($siswaUser, $this->makeKelas('x'));
        $this->daftarkan($siswa, $wajibA, Pendaftaran::STATUS_DITERIMA);

        $pendaftaranB = Pendaftaran::create([
            'siswa_id' => $siswa->id,
            'ekskul_id' => $wajibB->id,
            'tanggal_daftar' => now()->toDateString(),
            'status' => Pendaftaran::STATUS_PENDING,
            'alasan' => 'Daftar PMR',
        ]);

        $this->actingAs($ketuaUser)
            ->patch(route('ketua.pendaftaran.update', $pendaftaranB), [
                'status' => Pendaftaran::STATUS_DITERIMA,
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame(Pendaftaran::STATUS_PENDING, $pendaftaranB->fresh()->status);
    }

    public function test_siswa_bisa_mendaftar_dua_ekskul_non_wajib_tanpa_ekskul_wajib(): void
    {
        $silat = $this->makeEkskul(['nama_ekskul' => 'Silat Perisai Diri', 'kategori' => 'Bela Diri']);
        $basket = $this->makeEkskul(['nama_ekskul' => 'Bola Basket', 'kategori' => 'Olahraga']);

        $siswaUser = $this->makeUser('siswa');
        $siswa = $this->makeSiswa($siswaUser, $this->makeKelas('x'));

        // Siswa daftar Silat
        $this->daftarStore($siswaUser, $silat->id)
            ->assertRedirect()
            ->assertSessionHas('success');

        // Terima Silat
        Pendaftaran::where('siswa_id', $siswa->id)->where('ekskul_id', $silat->id)->update(['status' => Pendaftaran::STATUS_DITERIMA]);

        // Siswa daftar Basket (keduanya non-wajib, tanpa ekskul wajib sama sekali)
        $this->daftarStore($siswaUser, $basket->id)
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('pendaftarans', [
            'siswa_id' => $siswa->id,
            'ekskul_id' => $basket->id,
            'status' => Pendaftaran::STATUS_PENDING,
        ]);
    }
}
