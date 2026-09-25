<?php

namespace Tests\Feature;

use App\Models\Pendaftaran;
use App\Models\PengajuanKeluar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengajuanKeluarFlowTest extends TestCase
{
    use RefreshDatabase, \Tests\Support\CreatesFixtures;

    public function test_setelah_submit_pengajuan_siswa_tetap_anggota_aktif(): void
    {
        $ekskul = $this->makeEkskul();
        $this->makeKetua($ekskul);
        $siswaUser = $this->makeUser('siswa');
        $pendaftaran = $this->daftarkan($this->makeSiswa($siswaUser, $this->makeKelas('xii')), $ekskul);

        $this->actingAs($siswaUser)
            ->post(route('siswa.pengajuan-keluar.store'), ['alasan' => 'Saya ingin fokus ke pelajaran utama.'])
            ->assertSessionHas('success');

        $this->assertSame(Pendaftaran::STATUS_DITERIMA, $pendaftaran->fresh()->status);
        $this->assertNotNull(PengajuanKeluar::where('siswa_id', $pendaftaran->siswa_id)->first());

        $this->actingAs($siswaUser)
            ->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertSee('Aktif di '.$ekskul->nama_ekskul)
            ->assertDontSee('Kamu telah keluar dari ekskul')
            ->assertDontSee('Kamu dinonaktifkan dari ekskul');
    }

    public function test_ketua_menerima_pengajuan_menandai_status_keluar(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);
        $siswaUser = $this->makeUser('siswa');
        $pendaftaran = $this->daftarkan($this->makeSiswa($siswaUser, $this->makeKelas('xii')), $ekskul);

        $this->actingAs($siswaUser)
            ->post(route('siswa.pengajuan-keluar.store'), ['alasan' => 'Pindah ke ekskul lain.']);

        $pengajuan = PengajuanKeluar::first();

        $this->actingAs($ketua['user'])
            ->patch(route('ketua.pengajuan-keluar.update', $pengajuan), ['status' => 'diterima'])
            ->assertRedirect();

        $this->assertSame(Pendaftaran::STATUS_KELUAR, $pendaftaran->fresh()->status);

        $this->actingAs($siswaUser)
            ->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertSee('Kamu telah keluar dari ekskul');
    }

    public function test_ketua_menolak_pengajuan_siswa_tetap_anggota(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);
        $siswaUser = $this->makeUser('siswa');
        $pendaftaran = $this->daftarkan($this->makeSiswa($siswaUser, $this->makeKelas('xii')), $ekskul);

        $this->actingAs($siswaUser)
            ->post(route('siswa.pengajuan-keluar.store'), ['alasan' => 'Sudah tidak sejalan dengan jadwal ekskul.']);

        $pengajuan = PengajuanKeluar::first();

        $this->actingAs($ketua['user'])
            ->patch(route('ketua.pengajuan-keluar.update', $pengajuan), ['status' => 'ditolak'])
            ->assertRedirect();

        $this->assertSame(Pendaftaran::STATUS_DITERIMA, $pendaftaran->fresh()->status);
    }

    public function test_dinonaktifkan_oleh_ketua_tetap_status_nonaktif(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);
        $siswaUser = $this->makeUser('siswa');
        $pendaftaran = $this->daftarkan($this->makeSiswa($siswaUser, $this->makeKelas('xii')), $ekskul);

        $this->actingAs($ketua['user'])
            ->patch(route('ketua.anggota.update-status', $pendaftaran), ['status' => 'nonaktif'])
            ->assertRedirect();

        $this->assertSame(Pendaftaran::STATUS_NONAKTIF, $pendaftaran->fresh()->status);

        $this->actingAs($siswaUser)
            ->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertSee('Kamu dinonaktifkan dari ekskul')
            ->assertDontSee('Kamu telah keluar dari ekskul');
    }
}