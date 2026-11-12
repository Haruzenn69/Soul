<?php

namespace Tests\Feature;

use App\Models\Pendaftaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesFixtures;
use Tests\TestCase;

class MultiEkskulTest extends TestCase
{
    use CreatesFixtures, RefreshDatabase;

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
            ->assertSee('Ajukan permohonan keluar dari salah satu ekskul')
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
            ->assertSee('Switch ke Futsal')
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
            ->assertStatus(422);

        $this->assertSame(Pendaftaran::STATUS_PENDING, $pendaftaranB->fresh()->status);
    }

}
