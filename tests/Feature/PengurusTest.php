<?php

namespace Tests\Feature;

use App\Models\Pendaftaran;
use App\Models\Pengurus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesFixtures;
use Tests\TestCase;

class PengurusTest extends TestCase
{
    use CreatesFixtures, RefreshDatabase;

    public function test_pembina_bisa_melihat_halaman_pengurus_dan_menetapkan_langsung_dengan_status_aktif(): void
    {
        $ekskul = $this->makeEkskul();
        $pembina = $ekskul->pembina;

        $anggotaUser = $this->makeUser('siswa');
        $anggota = $this->makeSiswa($anggotaUser, $this->makeKelas('x'), 'siswa');
        $this->daftarkan($anggota, $ekskul, Pendaftaran::STATUS_DITERIMA);

        // Akses halaman pengurus pembina
        $this->actingAs($pembina->user)
            ->get(route('pembina.pengurus.index', ['ekskul' => $ekskul->id]))
            ->assertOk()
            ->assertSee('Kelola Pengurus Inti');

        // Pembina menetapkan bendahara langsung
        $this->actingAs($pembina->user)
            ->post(route('pembina.pengurus.tetapkan', $ekskul->id), [
                'siswa_id' => $anggota->id,
                'jabatan' => 'bendahara',
                'catatan' => 'Ditetapkan pembina',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('pengurus', [
            'ekskul_id' => $ekskul->id,
            'siswa_id' => $anggota->id,
            'jabatan' => 'bendahara',
            'status' => 'aktif',
            'diusulkan_oleh' => 'pembina',
        ]);
    }

    public function test_ketua_bisa_mengusulkan_wakil_sekretaris_bendahara_dengan_status_menunggu(): void
    {
        $ekskul = $this->makeEkskul();
        $ketuaData = $this->makeKetua($ekskul);

        $calonUser = $this->makeUser('siswa');
        $calon = $this->makeSiswa($calonUser, $this->makeKelas('x'), 'siswa');
        $this->daftarkan($calon, $ekskul, Pendaftaran::STATUS_DITERIMA);

        // Ketua mengusulkan sekretaris
        $this->actingAs($ketuaData['user'])
            ->post(route('ketua.pengurus.usulkan'), [
                'siswa_id' => $calon->id,
                'jabatan' => 'sekretaris',
                'catatan' => 'Rekomendasi dari ketua',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('pengurus', [
            'ekskul_id' => $ekskul->id,
            'siswa_id' => $calon->id,
            'jabatan' => 'sekretaris',
            'status' => 'menunggu',
            'diusulkan_oleh' => 'ketua',
        ]);
    }

    public function test_ketua_tidak_bisa_mengusulkan_posisi_ketua(): void
    {
        $ekskul = $this->makeEkskul();
        $ketuaData = $this->makeKetua($ekskul);

        $calonUser = $this->makeUser('siswa');
        $calon = $this->makeSiswa($calonUser, $this->makeKelas('x'), 'siswa');
        $this->daftarkan($calon, $ekskul, Pendaftaran::STATUS_DITERIMA);

        // Ketua mencoba mengusulkan ketua (harus divalidasi error)
        $this->actingAs($ketuaData['user'])
            ->post(route('ketua.pengurus.usulkan'), [
                'siswa_id' => $calon->id,
                'jabatan' => 'ketua',
            ])
            ->assertSessionHasErrors(['jabatan']);
    }

    public function test_pembina_bisa_menyetujui_dan_menolak_usulan_dari_ketua(): void
    {
        $ekskul = $this->makeEkskul();
        $pembina = $ekskul->pembina;

        $calon = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'), 'siswa');
        $this->daftarkan($calon, $ekskul, Pendaftaran::STATUS_DITERIMA);

        $usulan = Pengurus::create([
            'ekskul_id' => $ekskul->id,
            'siswa_id' => $calon->id,
            'jabatan' => 'wakil_ketua',
            'status' => 'menunggu',
            'diusulkan_oleh' => 'ketua',
        ]);

        // Pembina menyetujui
        $this->actingAs($pembina->user)
            ->post(route('pembina.pengurus.approve', $usulan->id))
            ->assertRedirect();

        $this->assertDatabaseHas('pengurus', [
            'id' => $usulan->id,
            'status' => 'aktif',
        ]);

        // Uji tolak
        $calon2 = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('xi'), 'siswa');
        $this->daftarkan($calon2, $ekskul, Pendaftaran::STATUS_DITERIMA);

        $usulan2 = Pengurus::create([
            'ekskul_id' => $ekskul->id,
            'siswa_id' => $calon2->id,
            'jabatan' => 'sekretaris',
            'status' => 'menunggu',
            'diusulkan_oleh' => 'ketua',
        ]);

        $this->actingAs($pembina->user)
            ->post(route('pembina.pengurus.reject', $usulan2->id), [
                'catatan' => 'Nilai akademik perlu ditingkatkan',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('pengurus', [
            'id' => $usulan2->id,
            'status' => 'ditolak',
        ]);
    }

    public function test_hanya_pembina_yang_bisa_mencopot_pengurus_aktif(): void
    {
        $ekskul = $this->makeEkskul();
        $pembina = $ekskul->pembina;
        $ketuaData = $this->makeKetua($ekskul);

        $anggota = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'), 'siswa');
        $this->daftarkan($anggota, $ekskul, Pendaftaran::STATUS_DITERIMA);

        $pengurus = Pengurus::create([
            'ekskul_id' => $ekskul->id,
            'siswa_id' => $anggota->id,
            'jabatan' => 'bendahara',
            'status' => 'aktif',
            'diusulkan_oleh' => 'pembina',
        ]);

        // Ketua mencoba mencopot (route copot ada di pembina, ketua harus ditolak/403)
        $this->actingAs($ketuaData['user'])
            ->post(route('pembina.pengurus.copot', $pengurus->id))
            ->assertForbidden();

        // Pembina yang sah mencopot
        $this->actingAs($pembina->user)
            ->post(route('pembina.pengurus.copot', $pengurus->id), [
                'catatan' => 'Pencopotan sukarela',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('pengurus', [
            'id' => $pengurus->id,
            'status' => 'selesai',
        ]);
    }
}
