<?php

namespace Tests\Feature;

use App\Models\Ekskul;
use App\Support\AturanEkskul;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesFixtures;
use Tests\TestCase;

class KesiswaanPembinaEkskulLimitTest extends TestCase
{
    use CreatesFixtures, RefreshDatabase;

    public function test_pembina_dapat_membina_hingga_5_ekskul(): void
    {
        $admin = $this->makeUser('admin');
        $pembina = $this->makePembina();

        // Assign 5 ekskul secara berurutan
        for ($i = 1; $i <= 5; $i++) {
            $ekskul = Ekskul::create([
                'nama_ekskul' => 'Ekskul ' . $i,
                'pembina_id' => null,
                'is_open_recruitment' => true,
            ]);

            $response = $this->actingAs($admin)
                ->post(route('kesiswaan.pembina.assign-ekskul', $pembina), [
                    'ekskul_id' => $ekskul->id,
                ]);

            $response->assertSessionHas('success');
            $this->assertEquals($pembina->id, $ekskul->fresh()->pembina_id);
        }

        $this->assertEquals(5, $pembina->ekskuls()->count());

        // Ekskul ke-6 harus gagal
        $ekskul6 = Ekskul::create([
            'nama_ekskul' => 'Ekskul 6',
            'pembina_id' => null,
            'is_open_recruitment' => true,
        ]);

        $response6 = $this->actingAs($admin)
            ->post(route('kesiswaan.pembina.assign-ekskul', $pembina), [
                'ekskul_id' => $ekskul6->id,
            ]);

        $response6->assertSessionHasErrors('ekskul_id');
        $this->assertNull($ekskul6->fresh()->pembina_id);
        $this->assertEquals(5, $pembina->ekskuls()->count());
    }

    public function test_create_ekskul_dengan_pembina_yang_sudah_membina_5_ekskul_gagal(): void
    {
        $admin = $this->makeUser('admin');
        $pembina = $this->makePembina();

        for ($i = 1; $i <= 5; $i++) {
            Ekskul::create([
                'nama_ekskul' => 'Ekskul ' . $i,
                'pembina_id' => $pembina->id,
                'is_open_recruitment' => true,
            ]);
        }

        $this->assertEquals(5, $pembina->ekskuls()->count());

        $response = $this->actingAs($admin)
            ->post(route('kesiswaan.ekskuls.store'), [
                'nama_ekskul' => 'Ekskul Baru',
                'kategori' => 'Olahraga',
                'pembina_id' => $pembina->id,
            ]);

        $response->assertSessionHasErrors('pembina_id');
        $this->assertEquals(5, $pembina->ekskuls()->count());
    }
}
