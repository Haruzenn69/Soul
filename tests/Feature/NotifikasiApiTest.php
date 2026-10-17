<?php

namespace Tests\Feature;

use App\Models\Notifikasi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesFixtures;
use Tests\TestCase;

class NotifikasiApiTest extends TestCase
{
    use CreatesFixtures, RefreshDatabase;

    public function test_siswa_dapat_menghapus_notifikasi_miliknya(): void
    {
        $user = $this->makeUser('siswa');
        $siswa = $this->makeSiswa($user, $this->makeKelas('x'));
        $notifikasi = Notifikasi::create([
            'siswa_id' => $siswa->id,
            'judul' => 'Pembaruan',
            'pesan' => 'Ada pembaruan.',
            'is_read' => false,
        ]);

        $this->actingAs($user)
            ->deleteJson('/api/siswa/notifikasi/'.$notifikasi->id)
            ->assertOk();

        $this->assertDatabaseMissing('notifikasis', ['id' => $notifikasi->id]);
    }

    public function test_siswa_tidak_dapat_menghapus_notifikasi_milik_siswa_lain(): void
    {
        $user = $this->makeUser('siswa');
        $this->makeSiswa($user, $this->makeKelas('x'));
        $otherUser = $this->makeUser('siswa');
        $otherSiswa = $this->makeSiswa($otherUser, $this->makeKelas('xi'));
        $notifikasi = Notifikasi::create([
            'siswa_id' => $otherSiswa->id,
            'judul' => 'Pembaruan',
            'pesan' => 'Ada pembaruan.',
            'is_read' => false,
        ]);

        $this->actingAs($user)
            ->deleteJson('/api/siswa/notifikasi/'.$notifikasi->id)
            ->assertForbidden();

        $this->assertDatabaseHas('notifikasis', ['id' => $notifikasi->id]);
    }
}
