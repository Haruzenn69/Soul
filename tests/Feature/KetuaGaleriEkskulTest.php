<?php

namespace Tests\Feature;

use App\Models\EkskulGaleri;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KetuaGaleriEkskulTest extends TestCase
{
    use RefreshDatabase, \Tests\Support\CreatesFixtures;

    public function test_ketua_mengupload_banyak_foto_galeri(): void
    {
        Storage::fake('public');

        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);

        $this->actingAs($ketua['user'])
            ->post(route('ketua.profil-ekskul.galeri-store'), [
                'foto' => [
                    UploadedFile::fake()->image('foto1.jpg'),
                    UploadedFile::fake()->image('foto2.jpg'),
                    UploadedFile::fake()->image('foto3.jpg'),
                ],
                'caption' => 'Momen latihan',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $galeris = EkskulGaleri::where('ekskul_id', $ekskul->id)->get();
        $this->assertCount(3, $galeris);
        $this->assertSame('Momen latihan', $galeris->first()->caption);

        foreach ($galeris as $galeri) {
            Storage::disk('public')->assertExists($galeri->foto);
        }
    }

    public function test_ketua_menghapus_foto_galeri(): void
    {
        Storage::fake('public');

        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);

        Storage::disk('public')->put('ekskul/galeri/hapus.jpg', 'foto');
        $galeri = $ekskul->galeris()->create(['foto' => 'ekskul/galeri/hapus.jpg']);

        $this->actingAs($ketua['user'])
            ->delete(route('ketua.profil-ekskul.galeri-destroy', $galeri))
            ->assertRedirect();

        $this->assertDatabaseMissing('ekskul_galeris', ['id' => $galeri->id]);
        Storage::disk('public')->assertMissing('ekskul/galeri/hapus.jpg');
    }

    public function test_ketua_tidak_bisa_menghapus_foto_galeri_ekskul_lain(): void
    {
        $ekskulA = $this->makeEkskul();
        $ketuaA = $this->makeKetua($ekskulA);
        $ekskulB = $this->makeEkskul();
        $this->makeKetua($ekskulB);

        $galeri = $ekskulB->galeris()->create(['foto' => 'ekskul/galeri/lain.jpg']);

        $this->actingAs($ketuaA['user'])
            ->delete(route('ketua.profil-ekskul.galeri-destroy', $galeri))
            ->assertForbidden();

        $this->assertDatabaseHas('ekskul_galeris', ['id' => $galeri->id]);
    }

    public function test_foto_galeri_tampil_di_katalog_ekskul(): void
    {
        $ekskul = $this->makeEkskul();
        $this->makeKetua($ekskul);

        $ekskul->galeris()->create(['foto' => 'ekskul/galeri/momen.jpg']);

        $this->get(route('ekskul.detail', $ekskul))
            ->assertOk()
            ->assertSee('Momen Kami')
            ->assertSee('storage/ekskul/galeri/momen.jpg');
    }
}