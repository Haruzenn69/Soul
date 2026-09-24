<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\Notifikasi;
use App\Models\Pembina;
use App\Models\Testimoni;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembinaTestimoniFaqTest extends TestCase
{
    use RefreshDatabase, \Tests\Support\CreatesFixtures;

    private function makeBinaan(Pembina $pembina): \App\Models\Ekskul
    {
        return $this->makeEkskul(['pembina_id' => $pembina->id]);
    }

    public function test_pembina_only_melihat_testimoni_ekskul_binaan(): void
    {
        $pembina = $this->makePembina();
        $ekskul = $this->makeBinaan($pembina);
        $ekskulLain = $this->makeEkskul();

        Testimoni::create(['ekskul_id' => $ekskul->id, 'nama' => 'Aulia', 'quote' => 'Testimoni milik binaan.', 'status' => Testimoni::STATUS_APPROVED]);
        Testimoni::create(['ekskul_id' => $ekskulLain->id, 'nama' => 'Bima', 'quote' => 'Testimoni ekskul lain.', 'status' => Testimoni::STATUS_APPROVED]);

        $this->actingAs($pembina->user)
            ->get(route('pembina.testimoni.index'))
            ->assertOk()
            ->assertSee('Aulia')
            ->assertDontSee('Bima');
    }

    public function test_pembina_only_melihat_faq_ekskul_binaan(): void
    {
        $pembina = $this->makePembina();
        $ekskul = $this->makeBinaan($pembina);
        $ekskulLain = $this->makeEkskul();

        Faq::create(['ekskul_id' => $ekskul->id, 'pertanyaan' => 'Pertanyaan binaan.', 'jawaban' => 'Jawaban.', 'status' => Faq::STATUS_ANSWERED]);
        Faq::create(['ekskul_id' => $ekskulLain->id, 'pertanyaan' => 'Pertanyaan ekskul lain.', 'jawaban' => 'Jawaban.', 'status' => Faq::STATUS_ANSWERED]);

        $this->actingAs($pembina->user)
            ->get(route('pembina.faq.index'))
            ->assertOk()
            ->assertSee('Pertanyaan binaan.')
            ->assertDontSee('Pertanyaan ekskul lain.');
    }

    public function test_pembina_tidak_bisa_moderasi_ekskul_bukan_binaan(): void
    {
        $pembina = $this->makePembina();
        $ekskulLain = $this->makeEkskul();

        $testimoni = Testimoni::create(['ekskul_id' => $ekskulLain->id, 'nama' => 'Bima', 'quote' => 'Konten.', 'status' => Testimoni::STATUS_PENDING]);
        $faq = Faq::create(['ekskul_id' => $ekskulLain->id, 'pertanyaan' => 'Tanya?', 'status' => Faq::STATUS_PENDING]);

        $responses = [
            fn () => $this->actingAs($pembina->user)->patch(route('pembina.testimoni.approve', $testimoni)),
            fn () => $this->actingAs($pembina->user)->patch(route('pembina.testimoni.reject', $testimoni)),
            fn () => $this->actingAs($pembina->user)->delete(route('pembina.testimoni.destroy', $testimoni)),
            fn () => $this->actingAs($pembina->user)->patch(route('pembina.faq.answer', $faq)),
            fn () => $this->actingAs($pembina->user)->delete(route('pembina.faq.destroy', $faq)),
        ];

        foreach ($responses as $call) {
            $call()->assertForbidden();
        }

        $this->assertSame(Testimoni::STATUS_PENDING, $testimoni->fresh()->status);
        $this->assertSame(Faq::STATUS_PENDING, $faq->fresh()->status);
    }

    public function test_pembina_approve_testimoni_memberi_notifikasi_siswa(): void
    {
        $pembina = $this->makePembina();
        $ekskul = $this->makeBinaan($pembina);
        $siswaUser = $this->makeUser('siswa');
        $this->makeSiswa($siswaUser, $this->makeKelas('x'));

        $testimoni = Testimoni::create([
            'ekskul_id' => $ekskul->id,
            'user_id' => $siswaUser->id,
            'nama' => 'Kontributor',
            'quote' => 'Kontribusi siswa.',
            'status' => Testimoni::STATUS_PENDING,
        ]);

        $this->actingAs($pembina->user)
            ->patch(route('pembina.testimoni.approve', $testimoni))
            ->assertRedirect();

        $this->assertSame(Testimoni::STATUS_APPROVED, $testimoni->fresh()->status);
        $this->assertSame(1, Notifikasi::where('siswa_id', $siswaUser->siswa->id)->where('judul', 'Testimoni Diterbitkan')->count());
    }

    public function test_pembina_reject_testimoni(): void
    {
        $pembina = $this->makePembina();
        $ekskul = $this->makeBinaan($pembina);

        $testimoni = Testimoni::create(['ekskul_id' => $ekskul->id, 'nama' => 'Aulia', 'quote' => 'Kalimat.', 'status' => Testimoni::STATUS_PENDING]);

        $this->actingAs($pembina->user)
            ->patch(route('pembina.testimoni.reject', $testimoni))
            ->assertRedirect();

        $this->assertSame(Testimoni::STATUS_REJECTED, $testimoni->fresh()->status);
    }

    public function test_pembina_menjawab_faq_memberi_notifikasi_siswa(): void
    {
        $pembina = $this->makePembina();
        $ekskul = $this->makeBinaan($pembina);
        $siswaUser = $this->makeUser('siswa');
        $this->makeSiswa($siswaUser, $this->makeKelas('x'));

        $faq = Faq::create([
            'ekskul_id' => $ekskul->id,
            'user_id' => $siswaUser->id,
            'pertanyaan' => 'Kapan jadwal latihan?',
            'status' => Faq::STATUS_PENDING,
        ]);

        $this->actingAs($pembina->user)
            ->patch(route('pembina.faq.answer', $faq), ['jawaban' => 'Setiap Sabtu sore.'])
            ->assertRedirect();

        $this->assertSame(Faq::STATUS_ANSWERED, $faq->fresh()->status);
        $this->assertSame('Setiap Sabtu sore.', $faq->fresh()->jawaban);
        $this->assertSame(1, Notifikasi::where('siswa_id', $siswaUser->siswa->id)->where('judul', 'Pertanyaan Kamu Terjawab')->count());
    }

    public function test_pembina_store_testimoni_manual_langsung_approved(): void
    {
        $pembina = $this->makePembina();
        $ekskul = $this->makeBinaan($pembina);

        $this->actingAs($pembina->user)
            ->post(route('pembina.testimoni.store'), [
                'ekskul_id' => $ekskul->id,
                'nama' => 'Aulia',
                'kelas' => '11 IPA 2',
                'quote' => 'Pengalaman yang luar biasa selama bergabung.',
            ])
            ->assertRedirect();

        $this->assertSame(1, Testimoni::where('ekskul_id', $ekskul->id)->where('status', Testimoni::STATUS_APPROVED)->count());
    }

    public function test_pembina_store_faq_manual_langsung_answered(): void
    {
        $pembina = $this->makePembina();
        $ekskul = $this->makeBinaan($pembina);

        $this->actingAs($pembina->user)
            ->post(route('pembina.faq.store'), [
                'ekskul_id' => $ekskul->id,
                'pertanyaan' => 'Apakah wajib hadir setiap kegiatan?',
                'jawaban' => 'Ya, kehadiran sangat diperhatikan.',
            ])
            ->assertRedirect();

        $this->assertSame(1, Faq::where('ekskul_id', $ekskul->id)->where('status', Faq::STATUS_ANSWERED)->count());
    }

    public function test_pembina_tidak_bisa_store_untuk_ekskul_bukan_binaan(): void
    {
        $pembina = $this->makePembina();
        $ekskulLain = $this->makeEkskul();

        $this->actingAs($pembina->user)
            ->post(route('pembina.testimoni.store'), [
                'ekskul_id' => $ekskulLain->id,
                'nama' => 'Bima',
                'quote' => 'Kalimat alasan yang benar.',
            ])
            ->assertForbidden();

        $this->assertSame(0, Testimoni::where('ekskul_id', $ekskulLain->id)->count());
    }

    public function test_pembina_filter_index_per_ekskul(): void
    {
        $pembina = $this->makePembina();
        $ekskulA = $this->makeBinaan($pembina);
        $ekskulB = $this->makeBinaan($pembina);

        Testimoni::create(['ekskul_id' => $ekskulA->id, 'nama' => 'Citra', 'quote' => 'Konten A.', 'status' => Testimoni::STATUS_APPROVED]);
        Testimoni::create(['ekskul_id' => $ekskulB->id, 'nama' => 'Dewi', 'quote' => 'Konten B.', 'status' => Testimoni::STATUS_APPROVED]);

        $this->actingAs($pembina->user)
            ->get(route('pembina.testimoni.index', ['ekskul' => $ekskulA->id]))
            ->assertOk()
            ->assertSee('Citra')
            ->assertDontSee('Dewi');
    }

    public function test_dashboard_pembina_menampilkan_antrian_pending(): void
    {
        $pembina = $this->makePembina();
        $ekskul = $this->makeBinaan($pembina);

        Testimoni::create(['ekskul_id' => $ekskul->id, 'nama' => 'Aulia', 'quote' => 'Kalimat konten.', 'status' => Testimoni::STATUS_PENDING]);
        Faq::create(['ekskul_id' => $ekskul->id, 'pertanyaan' => 'Pertanyaan?', 'status' => Faq::STATUS_PENDING]);

        $this->actingAs($pembina->user)
            ->get(route('pembina.dashboard'))
            ->assertOk()
            ->assertSee('Moderasi kontribusi siswa')
            ->assertSee('menunggu persetujuan')
            ->assertSee('menunggu jawaban')
            ->assertSee('Tambah Testimoni')
            ->assertSee('Tambah FAQ');
    }

    public function test_dashboard_pembina_bisa_input_testimoni_dan_faq(): void
    {
        $pembina = $this->makePembina();
        $ekskul = $this->makeBinaan($pembina);

        $this->actingAs($pembina->user)
            ->post(route('pembina.testimoni.store'), [
                'ekskul_id' => $ekskul->id,
                'nama' => 'Aulia',
                'quote' => 'Pengalaman luar biasa selama mengikuti ekskul.',
            ])
            ->assertRedirect();

        $this->actingAs($pembina->user)
            ->post(route('pembina.faq.store'), [
                'ekskul_id' => $ekskul->id,
                'pertanyaan' => 'Dimana lokasi latihan rutin?',
                'jawaban' => 'Di halaman sekolah bagian utara.',
            ])
            ->assertRedirect();

        $this->assertSame(1, Testimoni::where('ekskul_id', $ekskul->id)->where('status', Testimoni::STATUS_APPROVED)->count());
        $this->assertSame(1, Faq::where('ekskul_id', $ekskul->id)->where('status', Faq::STATUS_ANSWERED)->count());
    }
}
