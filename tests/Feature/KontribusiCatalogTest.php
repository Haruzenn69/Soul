<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\Notifikasi;
use App\Models\Testimoni;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KontribusiCatalogTest extends TestCase
{
    use RefreshDatabase, \Tests\Support\CreatesFixtures;

    private function catalogPath($ekskul): string
    {
        return route('ekskul.detail', $ekskul);
    }

    public function test_siswa_mengirim_testimoni_menjadi_pending_dan_memberi_notif_ketua(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);
        $siswaUser = $this->makeUser('siswa');
        $siswa = $this->makeSiswa($siswaUser, $this->makeKelas('xii'));

        $response = $this->actingAs($siswaUser)
            ->post(route('ekskul.testimoni.store', $ekskul), ['quote' => 'Sangat seru ekskulnya!']);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $testimoni = Testimoni::first();
        $this->assertNotNull($testimoni);
        $this->assertSame(Testimoni::STATUS_PENDING, $testimoni->status);
        $this->assertSame($siswaUser->id, $testimoni->user_id);
        $this->assertSame($siswa->nama, $testimoni->nama);
        $this->assertSame($siswa->kelas->nama, $testimoni->kelas);

        $this->assertDatabaseHas('notifikasis', [
            'siswa_id' => $ketua['siswa']->id,
            'judul' => 'Testimoni Baru Masuk',
        ]);
    }

    public function test_testimoni_pending_tidak_tampil_di_katalog(): void
    {
        $ekskul = $this->makeEkskul();
        $this->makeKetua($ekskul);
        $siswaUser = $this->makeUser('siswa');
        $this->makeSiswa($siswaUser, $this->makeKelas('xii'));

        $this->actingAs($siswaUser)
            ->post(route('ekskul.testimoni.store', $ekskul), ['quote' => 'Belum disetujui.']);

        $this->get($this->catalogPath($ekskul))
            ->assertOk()
            ->assertDontSee('Belum disetujui.');
    }

    public function test_siswa_tidak_bisa_mengirim_testimoni_duplikat(): void
    {
        $ekskul = $this->makeEkskul();
        $this->makeKetua($ekskul);
        $siswaUser = $this->makeUser('siswa');
        $this->makeSiswa($siswaUser, $this->makeKelas('xii'));

        $this->actingAs($siswaUser)
            ->post(route('ekskul.testimoni.store', $ekskul), ['quote' => 'Pertama.']);

        $response = $this->actingAs($siswaUser)
            ->post(route('ekskul.testimoni.store', $ekskul), ['quote' => 'Duplikat.']);

        $response->assertStatus(422);
        $this->assertSame(1, Testimoni::count());
    }

    public function test_kesiswaan_diblokir_dari_mengirim_testimoni(): void
    {
        $ekskul = $this->makeEkskul();
        $kesiswaan = $this->makeUser('kesiswaan');

        $this->actingAs($kesiswaan)
            ->post(route('ekskul.testimoni.store', $ekskul), ['quote' => 'Halo.'])
            ->assertForbidden();
    }

    public function test_ketua_menyetujui_testimoni_dan_siswa_dapat_notif(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);
        $siswaUser = $this->makeUser('siswa');
        $this->makeSiswa($siswaUser, $this->makeKelas('xii'));

        $this->actingAs($siswaUser)
            ->post(route('ekskul.testimoni.store', $ekskul), ['quote' => 'Menunggu approval.']);

        $testimoni = Testimoni::first();

        $response = $this->actingAs($ketua['user'])
            ->patch(route('ketua.testimoni.approve', $testimoni));

        $response->assertRedirect();
        $this->assertSame(Testimoni::STATUS_APPROVED, $testimoni->fresh()->status);

        $this->assertDatabaseHas('notifikasis', [
            'siswa_id' => $siswaUser->siswa->id,
            'judul' => 'Testimoni Diterbitkan',
        ]);

        $this->actingAs($siswaUser)
            ->get($this->catalogPath($ekskul))
            ->assertOk()
            ->assertSee('Menunggu approval.');
    }

    public function test_ketua_menolak_testimoni(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);
        $siswaUser = $this->makeUser('siswa');
        $this->makeSiswa($siswaUser, $this->makeKelas('xii'));

        $this->actingAs($siswaUser)
            ->post(route('ekskul.testimoni.store', $ekskul), ['quote' => 'Akan ditolak.']);

        $testimoni = Testimoni::first();

        $this->actingAs($ketua['user'])
            ->patch(route('ketua.testimoni.reject', $testimoni))
            ->assertRedirect();

        $this->assertSame(Testimoni::STATUS_REJECTED, $testimoni->fresh()->status);
    }

    public function test_siswa_bertanya_dan_ketua_menjawab_faq(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);
        $siswaUser = $this->makeUser('siswa');
        $this->makeSiswa($siswaUser, $this->makeKelas('xii'));

        $this->actingAs($siswaUser)
            ->post(route('ekskul.faq.store', $ekskul), ['pertanyaan' => 'Apakah butuh pengalaman dulu?']);

        $faq = Faq::first();
        $this->assertNotNull($faq);
        $this->assertSame(Faq::STATUS_PENDING, $faq->status);

        $this->assertDatabaseHas('notifikasis', [
            'siswa_id' => $ketua['siswa']->id,
            'judul' => 'Pertanyaan Baru dari Siswa',
        ]);

        $this->actingAs($ketua['user'])
            ->patch(route('ketua.faq.answer', $faq), ['jawaban' => 'Tidak, semua boleh gabung.'])
            ->assertRedirect();

        $this->assertSame(Faq::STATUS_ANSWERED, $faq->fresh()->status);
        $this->assertSame('Tidak, semua boleh gabung.', $faq->fresh()->jawaban);

        $this->assertDatabaseHas('notifikasis', [
            'siswa_id' => $siswaUser->siswa->id,
            'judul' => 'Pertanyaan Kamu Terjawab',
        ]);
    }

    public function test_faq_pending_tidak_tampil_di_katalog_dan_yang_dijawab_tampil(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);
        $siswaUser = $this->makeUser('siswa');
        $this->makeSiswa($siswaUser, $this->makeKelas('xii'));

        $this->actingAs($siswaUser)
            ->post(route('ekskul.faq.store', $ekskul), ['pertanyaan' => 'Quest rahasia?']);

        $faq = Faq::first();
        $this->actingAs($ketua['user'])
            ->patch(route('ketua.faq.answer', $faq), ['jawaban' => 'Tidak ada rahasia.']);

        $this->actingAs($siswaUser)
            ->get($this->catalogPath($ekskul))
            ->assertOk()
            ->assertSee('Tidak ada rahasia.');
    }

    public function test_dashboard_siswa_menampilkan_form_testimoni_dan_faq_untuk_anggota(): void
    {
        $ekskul = $this->makeEkskul();
        $this->makeKetua($ekskul);
        $siswaUser = $this->makeUser('siswa');
        $this->daftarkan($this->makeSiswa($siswaUser, $this->makeKelas('xii')), $ekskul);

        $this->actingAs($siswaUser)
            ->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertSee('Pertanyaan ke Ketua')
            ->assertSee('Kirim Testimoni')
            ->assertSee('/ekskul/'.$ekskul->id.'/testimoni');
    }

    public function test_dashboard_siswa_tidak_menampilkan_form_untuk_non_anggota(): void
    {
        $kelas = $this->makeKelas('xii');
        $siswaUser = $this->makeUser('siswa');
        $this->makeSiswa($siswaUser, $kelas);

        $this->actingAs($siswaUser)
            ->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertDontSee('Pertanyaan ke Ketua');
    }

    public function test_dashboard_siswa_hidden_setelah_mengirim_testimoni(): void
    {
        $ekskul = $this->makeEkskul();
        $this->makeKetua($ekskul);
        $siswaUser = $this->makeUser('siswa');
        $this->daftarkan($this->makeSiswa($siswaUser, $this->makeKelas('xii')), $ekskul);

        $this->actingAs($siswaUser)
            ->post(route('ekskul.testimoni.store', $ekskul), ['quote' => 'Sudah kirim.']);

        $this->actingAs($siswaUser)
            ->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertDontSee('Tulis pengalamanmu')
            ->assertSee('Kamu sudah mengirim testimoni');
    }

    public function test_ketua_tidak_bisa_menyetujui_testimoni_ekskul_lain(): void
    {
        $ekskulA = $this->makeEkskul();
        $this->makeKetua($ekskulA);
        $ekskulB = $this->makeEkskul();
        $ketuaB = $this->makeKetua($ekskulB);

        $testimoni = $ekskulA->testimoniss()->create([
            'nama' => 'Anonim',
            'quote' => 'Punya ekskul A.',
            'status' => Testimoni::STATUS_PENDING,
        ]);

        $this->actingAs($ketuaB['user'])
            ->patch(route('ketua.testimoni.approve', $testimoni))
            ->assertForbidden();

        $this->assertSame(Testimoni::STATUS_PENDING, $testimoni->fresh()->status);
    }
}