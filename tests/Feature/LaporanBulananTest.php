<?php

namespace Tests\Feature;

use App\Models\LaporanBulanan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesFixtures;
use Tests\TestCase;

class LaporanBulananTest extends TestCase
{
    use CreatesFixtures, RefreshDatabase;

    public function test_laporan_bulan_yang_belum_diselesaikan_memblokir_duplikasi(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);

        LaporanBulanan::create([
            'ekskul_id' => $ekskul->id,
            'bulan' => now()->format('Y-m'),
            'materi_kegiatan' => 'Materi latihan',
            'kehadiran' => 'Semua hadir',
            'status' => LaporanBulanan::STATUS_DRAFT,
        ]);

        $this->actingAs($ketua['user'])
            ->post('/ketua/laporan-bulanan', ['tujuan' => 'Tujuan latihan bulan ini'])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame(1, LaporanBulanan::count());
    }

    public function test_ketua_tidak_bisa_melihat_laporan_ekskul_lain(): void
    {
        $ekskulA = $this->makeEkskul();
        $ketuaA = $this->makeKetua($ekskulA);

        $ekskulB = $this->makeEkskul();
        $laporanB = LaporanBulanan::create([
            'ekskul_id' => $ekskulB->id,
            'bulan' => now()->format('Y-m'),
            'status' => LaporanBulanan::STATUS_DRAFT,
        ]);

        $this->actingAs($ketuaA['user'])
            ->get('/ketua/laporan-bulanan/'.$laporanB->id)
            ->assertForbidden();
    }

    public function test_pembina_hanya_bisa_menyetujui_laporan_ekskulnya_sendiri(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);

        $ekskulLain = $this->makeEkskul();
        $laporanLain = LaporanBulanan::create([
            'ekskul_id' => $ekskulLain->id,
            'bulan' => now()->format('Y-m'),
            'status' => LaporanBulanan::STATUS_MENUNGGU,
        ]);

        $this->actingAs($ekskul->pembina->user)
            ->post('/pembina/laporan/'.$laporanLain->id.'/approve')
            ->assertForbidden();
    }

    public function test_pembina_menyetujui_laporan_dan_ketua_mendapat_notifikasi(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);

        $laporan = LaporanBulanan::create([
            'ekskul_id' => $ekskul->id,
            'bulan' => now()->format('Y-m'),
            'materi_kegiatan' => 'Materi latihan',
            'kehadiran' => 'Semua hadir',
            'status' => LaporanBulanan::STATUS_MENUNGGU,
        ]);

        $this->actingAs($ekskul->pembina->user)
            ->post('/pembina/laporan/'.$laporan->id.'/approve')
            ->assertRedirect(route('pembina.laporan.show', $laporan));

        $this->assertSame(LaporanBulanan::STATUS_DISETUJUI, $laporan->fresh()->status);

        $this->assertDatabaseHas('notifikasis', [
            'siswa_id' => $ketua['siswa']->id,
            'laporan_bulanan_id' => $laporan->id,
            'tipe' => 'diterima',
            'judul' => 'Laporan Disetujui',
        ]);
    }
}
