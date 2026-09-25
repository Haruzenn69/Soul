<?php

namespace Tests\Feature;

use App\Models\Kelas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesFixtures;
use Tests\TestCase;

class KelasControllerTest extends TestCase
{
    use CreatesFixtures, RefreshDatabase;

    private function asKesiswaan()
    {
        return $this->actingAs($this->makeUser('kesiswaan'));
    }

    public function test_bisa_membuat_kelas_dengan_jurusan_br(): void
    {
        $tahunAjaran = $this->makeTahunAjaran();

        $response = $this->asKesiswaan()->post(route('kesiswaan.kelas.store'), [
            'tingkat' => 'xi',
            'jurusan' => 'br',
            'rombel' => 3,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('kelas', [
            'nama' => '11 BR 3',
            'tingkat' => 'xi',
            'jurusan' => 'br',
            'rombel' => 3,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);
    }

    public function test_semua_jurusan_yang_terdaftar_bisa_dibuat(): void
    {
        $tahunAjaran = $this->makeTahunAjaran();

        foreach (array_keys(config('kelas.jurusan')) as $jurusan) {
            $this->asKesiswaan()->post(route('kesiswaan.kelas.store'), [
                'tingkat' => 'xi',
                'jurusan' => $jurusan,
                'rombel' => 1,
                'tahun_ajaran_id' => $tahunAjaran->id,
            ])->assertSessionHasNoErrors();
        }

        $this->assertCount(count(array_keys(config('kelas.jurusan'))), Kelas::where('tahun_ajaran_id', $tahunAjaran->id)->get());
    }
}
