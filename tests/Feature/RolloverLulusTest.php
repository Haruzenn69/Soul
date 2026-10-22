<?php

namespace Tests\Feature;

use App\Models\Ekskul;
use App\Models\Kelas;
use App\Models\Pendaftaran;
use App\Models\TahunAjaran;
use App\Services\AcademicYearRolloverService;
use App\Services\ArsipEkskulService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesFixtures;
use Tests\TestCase;

class RolloverLulusTest extends TestCase
{
    use CreatesFixtures, RefreshDatabase;

    public function test_aktivasi_tahun_ajarannya_menutup_keanggotaan_aktif_siswa_lulus(): void
    {
        $tahunLama = $this->makeTahunAjaran();
        $kelasXii = Kelas::create([
            'nama' => 'XII PPLG 1',
            'tingkat' => 'xii',
            'tahun_ajaran_id' => $tahunLama->id,
        ]);
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $kelasXii);

        $diterima = $this->daftarkan($siswa, $this->makeEkskul(), Pendaftaran::STATUS_DITERIMA);
        $peringatan = $this->daftarkan($siswa, $this->makeEkskul(), Pendaftaran::STATUS_PERINGATAN);
        $nonaktif = $this->daftarkan($siswa, $this->makeEkskul(), Pendaftaran::STATUS_NONAKTIF);
        $pending = $this->daftarkan($siswa, $this->makeEkskul(), Pendaftaran::STATUS_PENDING);

        $tahunBaru = TahunAjaran::create(['nama' => 'Tahun Baru']);

        app(AcademicYearRolloverService::class)->activateTahunAjaran($tahunBaru);

        $this->assertSame(Pendaftaran::STATUS_LULUS, $diterima->fresh()->status);
        $this->assertSame(Pendaftaran::STATUS_LULUS, $peringatan->fresh()->status);
        $this->assertSame(Pendaftaran::STATUS_NONAKTIF, $nonaktif->fresh()->status);
        $this->assertSame(Pendaftaran::STATUS_PENDING, $pending->fresh()->status);

        $this->assertSame('nonaktif', $siswa->fresh()->status);
        $this->assertFalse($siswa->user->fresh()->is_active);
        $this->assertSame('aktif', $tahunBaru->fresh()->status);
    }

    public function test_arsip_menampilkan_pendaftaran_lulus_setelah_rollover(): void
    {
        $tahunLama = $this->makeTahunAjaran();
        $kelasXii = Kelas::create([
            'nama' => 'XII RPL 2',
            'tingkat' => 'xii',
            'tahun_ajaran_id' => $tahunLama->id,
        ]);
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $kelasXii);

        $diterima = $this->daftarkan($siswa, $this->makeEkskul(), Pendaftaran::STATUS_DITERIMA);
        $nonaktif = $this->daftarkan($siswa, $this->makeEkskul(), Pendaftaran::STATUS_NONAKTIF);
        $keluar = $this->daftarkan($siswa, $this->makeEkskul(), Pendaftaran::STATUS_KELUAR);

        app(AcademicYearRolloverService::class)->activateTahunAjaran(
            TahunAjaran::create(['nama' => 'Tahun Baru Arsip'])
        );

        $arsip = app(ArsipEkskulService::class)->arsip(Ekskul::whereIn('id', [$diterima->ekskul_id, $nonaktif->ekskul_id, $keluar->ekskul_id])->get());

        $this->assertSame([$diterima->id], $arsip['anggotaLulus']->pluck('id')->all());
        $this->assertSame(1, $arsip['total']);
        $this->assertFalse($arsip['anggotaLulus']->contains('id', $nonaktif->id));
        $this->assertFalse($arsip['anggotaLulus']->contains('id', $keluar->id));
    }
}
