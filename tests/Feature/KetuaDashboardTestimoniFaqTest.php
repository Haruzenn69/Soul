<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\Testimoni;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KetuaDashboardTestimoniFaqTest extends TestCase
{
    use RefreshDatabase, \Tests\Support\CreatesFixtures;

    public function test_dashboard_ketua_menampilkan_form_testimoni_dan_faq(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);

        $this->actingAs($ketua['user'])
            ->get(route('ketua.dashboard'))
            ->assertOk()
            ->assertSee('Tambah Testimoni')
            ->assertSee('Tambah FAQ')
            ->assertSee('Moderasi kontribusi siswa', false)
            ->assertSee('tidak ada antrian');
    }

    public function test_dashboard_ketua_bisa_input_testimoni_dan_faq(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);

        $this->actingAs($ketua['user'])
            ->post(route('ketua.testimoni.store'), [
                'nama' => 'Reza',
                'kelas' => '12 IPA 1',
                'quote' => 'Banyak pengalaman berharga yang saya dapatkan.',
            ])
            ->assertRedirect();

        $this->actingAs($ketua['user'])
            ->post(route('ketua.faq.store'), [
                'pertanyaan' => 'Apakah wajib hadir setiap pertemuan?',
                'jawaban' => 'Ya, kehadiran dicatat dan diperhatikan.',
            ])
            ->assertRedirect();

        $this->assertSame(1, Testimoni::where('ekskul_id', $ekskul->id)->where('status', Testimoni::STATUS_APPROVED)->count());
        $this->assertSame(1, Faq::where('ekskul_id', $ekskul->id)->where('status', Faq::STATUS_ANSWERED)->count());
    }
}