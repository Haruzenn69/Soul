<?php

namespace Tests\Feature;

use App\Models\Ekskul;
use App\Models\Pendaftaran;
use App\Models\RiwayatJabatan;
use App\Models\Siswa;
use App\Models\User;
use App\Services\ArsipEkskulService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesFixtures;
use Tests\TestCase;

class ArsipAnggotaTest extends TestCase
{
    use CreatesFixtures, RefreshDatabase;

    public function test_arsip_tidak_muncul_sebelum_filter_diaktifkan(): void
    {
        [$user, $ekskul] = $this->setupEkskul();
        $this->buatArsip($ekskul);

        $response = $this->actingAs($user)->get(route('pembina.anggota'));

        $response->assertOk();
        $response->assertDontSee('Arsip Anggota &amp; Ketua Sebelumnya', false);
    }

    public function test_arsip_muncul_setelah_filter_diaktifkan(): void
    {
        [$user, $ekskul] = $this->setupEkskul();
        $this->buatArsip($ekskul);

        $response = $this->actingAs($user)->get(route('pembina.anggota', ['arsip' => 1]));

        $response->assertOk();
        $response->assertSee('Arsip Anggota &amp; Ketua Sebelumnya', false);
        $response->assertSee('Ketua Sebelumnya');
        $response->assertSee('Anggota Nonaktif &amp; Keluar', false);
    }

    public function test_choosing_a_new_ketua_closes_the_previous_period(): void
    {
        [$user, $ekskul, $lama, $baru] = $this->setupEkskulDenganDuaSiswa();
        $service = app(ArsipEkskulService::class);
        $service->mulaiPeriodeKetua($ekskul, $lama, now()->subMonths(6));

        $this->actingAs($user)
            ->post(route('pembina.ekskul.pilih-ketua', [$ekskul, $baru]))
            ->assertRedirect();

        $periodeLama = RiwayatJabatan::where('siswa_id', $lama->id)->first();
        $this->assertNotNull($periodeLama);
        $this->assertTrue($periodeLama->isFinished());
        $this->assertSame(RiwayatJabatan::ALASAN_DIGANTI, $periodeLama->alasan_selesai);

        $periodeBaru = RiwayatJabatan::where('siswa_id', $baru->id)->first();
        $this->assertNotNull($periodeBaru);
        $this->assertTrue($periodeBaru->isActive());
    }

    public function test_copot_ketua_closes_the_period_as_arsip(): void
    {
        [$user, $ekskul, $ketua] = $this->setupEkskulDenganDuaSiswa();
        app(ArsipEkskulService::class)->mulaiPeriodeKetua($ekskul, $ketua);

        $this->actingAs($user)
            ->post(route('pembina.ekskul.copot-ketua', [$ekskul, $ketua]))
            ->assertRedirect();

        $this->assertSame('siswa', $ketua->fresh()->jabatan);
        $this->assertTrue(RiwayatJabatan::where('siswa_id', $ketua->id)->first()->isFinished());
    }

    public function test_pembina_tidak_bisa_mengoreksi_periode_ekskul_orang_lain(): void
    {
        [$user, $ekskul] = $this->setupEkskul();
        $asing = $this->makeEkskul();
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));
        $this->daftarkan($siswa, $asing);
        $periode = RiwayatJabatan::create([
            'siswa_id' => $siswa->id,
            'ekskul_id' => $asing->id,
            'jabatan' => RiwayatJabatan::JABATAN_KETUA,
            'mulai' => now()->subYear()->toDateString(),
            'selesai' => now()->subMonth()->toDateString(),
        ]);

        $this->actingAs($user)
            ->patch(route('pembina.arsip.periode-ketua', $periode), [
                'mulai' => now()->subYears(2)->toDateString(),
                'selesai' => now()->subMonths(2)->toDateString(),
            ])
            ->assertForbidden();
    }

    public function test_koreksi_periode_menyimpan_tanggal_baru(): void
    {
        [$user, $ekskul] = $this->setupEkskul();
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));
        $this->daftarkan($siswa, $ekskul);
        $periode = RiwayatJabatan::create([
            'siswa_id' => $siswa->id,
            'ekskul_id' => $ekskul->id,
            'jabatan' => RiwayatJabatan::JABATAN_KETUA,
            'mulai' => now()->subYear()->toDateString(),
            'selesai' => now()->subMonth()->toDateString(),
            'alasan_selesai' => RiwayatJabatan::ALASAN_DIGANTI,
        ]);

        $mulai = now()->subYears(2)->startOfYear()->toDateString();

        $this->actingAs($user)
            ->patch(route('pembina.arsip.periode-ketua', $periode), ['mulai' => $mulai, 'selesai' => ''])
            ->assertRedirect();

        $periode->refresh();
        $this->assertSame($mulai, $periode->mulai->toDateString());
        $this->assertNull($periode->selesai);
    }

    public function test_tanggal_selesai_tidak_boleh_lebih_awal_dari_mulai(): void
    {
        [$user, $ekskul] = $this->setupEkskul();
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));
        $this->daftarkan($siswa, $ekskul);
        $periode = RiwayatJabatan::create([
            'siswa_id' => $siswa->id,
            'ekskul_id' => $ekskul->id,
            'jabatan' => RiwayatJabatan::JABATAN_KETUA,
            'mulai' => now()->subYear()->toDateString(),
            'selesai' => now()->subMonth()->toDateString(),
        ]);

        $this->actingAs($user)
            ->patch(route('pembina.arsip.periode-ketua', $periode), [
                'mulai' => now()->toDateString(),
                'selesai' => now()->subYear()->toDateString(),
            ])
            ->assertSessionHasErrors('selesai');
    }

    public function test_halaman_anggota_ketua_menampilkan_arsip(): void
    {
        [$ketuaUser, $ekskul, $ketua] = $this->setupEkskulDenganDuaSiswa();
        app(ArsipEkskulService::class)->mulaiPeriodeKetua($ekskul, $ketua);

        // Copot + tutup periode agar ada satu periode selesai.
        app(ArsipEkskulService::class)->akhiriPeriodeKetua($ekskul, $ketua);

        $nonaktif = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));
        $this->daftarkan($nonaktif, $ekskul, Pendaftaran::STATUS_NONAKTIF);

        $this->actingAs($ketuaUser)
            ->get(route('ketua.anggota.index', ['arsip' => 1]))
            ->assertOk()
            ->assertSee('Arsip Anggota &amp; Ketua Sebelumnya', false)
            ->assertSee('Anggota Nonaktif &amp; Keluar', false);
    }

    public function test_anggota_nonaktif_tidak_ditampilkan_tanpa_filter(): void
    {
        [$ketuaUser, $ekskul] = $this->setupEkskulDenganKetua();
        $nonaktif = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));
        $this->daftarkan($nonaktif, $ekskul, Pendaftaran::STATUS_NONAKTIF);

        $this->actingAs($ketuaUser)
            ->get(route('ketua.anggota.index'))
            ->assertOk()
            ->assertDontSee('Arsip Anggota &amp; Ketua Sebelumnya')
            ->assertDontSee($nonaktif->nama);
    }

    /**
     * @return array{0: User, 1: Ekskul}
     */
    private function setupEkskul(): array
    {
        $pembina = $this->makePembina();
        $user = $pembina->user;
        $ekskul = Ekskul::create([
            'pembina_id' => $pembina->id,
            'nama_ekskul' => 'Ekskul Arsip',
            'deskripsi' => fake()->sentence(),
            'is_open_recruitment' => true,
        ]);

        return [$user, $ekskul];
    }

    /**
     * @return array{0: User, 1: Ekskul}
     */
    private function setupEkskulDenganKetua(): array
    {
        [$user, $ekskul] = $this->setupEkskul();
        $this->makeKetua($ekskul);

        return [$user, $ekskul];
    }

    /**
     * @return array{0: User, 1: Ekskul, 2: Siswa, 3: Siswa}
     */
    private function setupEkskulDenganDuaSiswa(): array
    {
        [$user, $ekskul] = $this->setupEkskulDenganKetua();
        $ketua = $ekskul->ketua();

        $baru = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));
        $this->daftarkan($baru, $ekskul);

        return [$user, $ekskul, $ketua, $baru];
    }

    private function buatArsip(Ekskul $ekskul): void
    {
        $lama = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));
        $this->daftarkan($lama, $ekskul);

        RiwayatJabatan::create([
            'siswa_id' => $lama->id,
            'ekskul_id' => $ekskul->id,
            'jabatan' => RiwayatJabatan::JABATAN_KETUA,
            'mulai' => now()->subYear()->toDateString(),
            'selesai' => now()->subMonths(2)->toDateString(),
            'alasan_selesai' => RiwayatJabatan::ALASAN_DIGANTI,
        ]);

        $nonaktif = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));
        $this->daftarkan($nonaktif, $ekskul, Pendaftaran::STATUS_NONAKTIF);
    }
}
