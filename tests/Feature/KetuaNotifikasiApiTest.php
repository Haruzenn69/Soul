<?php

namespace Tests\Feature;

use App\Models\Notifikasi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesFixtures;
use Tests\TestCase;

class KetuaNotifikasiApiTest extends TestCase
{
    use CreatesFixtures, RefreshDatabase;

    public function test_ketua_dapat_menghapus_notifikasi_miliknya_sendiri(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);
        $notif = $this->buatNotifikasi($ketua['siswa']->id);

        $this->actingAs($ketua['user'])
            ->deleteJson('/api/ketua/notifikasi/'.$notif->id)
            ->assertOk();

        $this->assertDatabaseMissing('notifikasis', ['id' => $notif->id]);
    }

    public function test_ketua_tidak_bisa_menghapus_notifikasi_orang_lain(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);
        $anggota = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));
        $notif = $this->buatNotifikasi($anggota->id);

        $this->actingAs($ketua['user'])
            ->deleteJson('/api/ketua/notifikasi/'.$notif->id)
            ->assertForbidden();

        $this->assertDatabaseHas('notifikasis', ['id' => $notif->id]);
    }

    public function test_clear_all_api_ketua_hanya_menghapus_notifikasi_sendiri(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);
        $anggota = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));

        $milikKetua = $this->buatNotifikasi($ketua['siswa']->id);
        $milikAnggota = $this->buatNotifikasi($anggota->id);

        $this->actingAs($ketua['user'])
            ->deleteJson('/api/ketua/notifikasi/clear-all')
            ->assertOk();

        $this->assertDatabaseMissing('notifikasis', ['id' => $milikKetua->id]);
        $this->assertDatabaseHas('notifikasis', ['id' => $milikAnggota->id]);
    }

    private function buatNotifikasi(int $siswaId): Notifikasi
    {
        return Notifikasi::create([
            'siswa_id' => $siswaId,
            'judul' => 'Pengumuman',
            'pesan' => 'Isi notifikasi untuk pengujian.',
        ]);
    }
}
