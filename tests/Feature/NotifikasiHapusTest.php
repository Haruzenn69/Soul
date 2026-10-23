<?php

namespace Tests\Feature;

use App\Models\Notifikasi;
use App\Models\Pembina;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesFixtures;
use Tests\TestCase;

class NotifikasiHapusTest extends TestCase
{
    use CreatesFixtures, RefreshDatabase;

    public function test_siswa_dapat_menghapus_notifikasinya_sendiri(): void
    {
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));
        $notif = $this->buatNotifikasiSiswa($siswa->id);

        $this->actingAs($siswa->user)
            ->delete(route('siswa.notifikasi.destroy', $notif))
            ->assertRedirect();

        $this->assertDatabaseMissing('notifikasis', ['id' => $notif->id]);
    }

    public function test_siswa_tidak_bisa_menghapus_notifikasi_orang_lain(): void
    {
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));
        $lain = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));
        $notif = $this->buatNotifikasiSiswa($lain->id);

        $this->actingAs($siswa->user)
            ->delete(route('siswa.notifikasi.destroy', $notif))
            ->assertStatus(403);

        $this->assertDatabaseHas('notifikasis', ['id' => $notif->id]);
    }

    public function test_clear_all_siswa_hanya_menghapus_notifikasi_sendiri(): void
    {
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));
        $lain = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));
        $milikSendiri = $this->buatNotifikasiSiswa($siswa->id);
        $milikOrangLain = $this->buatNotifikasiSiswa($lain->id);

        $this->actingAs($siswa->user)
            ->delete(route('siswa.notifikasi.clear-all'))
            ->assertRedirect();

        $this->assertDatabaseMissing('notifikasis', ['id' => $milikSendiri->id]);
        $this->assertDatabaseHas('notifikasis', ['id' => $milikOrangLain->id]);
    }

    public function test_ketua_dapat_menghapus_notifikasi_tetapi_bukan_milik_siswa_lain(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);
        $anggota = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));

        $notifKetua = $this->buatNotifikasiSiswa($ketua['siswa']->id, 'Pendaftaran Baru Masuk');
        $notifAnggota = $this->buatNotifikasiSiswa($anggota->id, 'Pendaftaran Diterima');

        $this->actingAs($ketua['user'])
            ->delete(route('ketua.notifikasi.destroy', $notifKetua))
            ->assertRedirect();

        $this->assertDatabaseMissing('notifikasis', ['id' => $notifKetua->id]);

        $this->actingAs($ketua['user'])
            ->delete(route('ketua.notifikasi.destroy', $notifAnggota))
            ->assertStatus(403);

        $this->assertDatabaseHas('notifikasis', ['id' => $notifAnggota->id]);
    }

    public function test_clear_all_ketua_hanya_menghapus_notifikasi_sendiri(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);
        $anggota = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));

        $milikKetua = $this->buatNotifikasiSiswa($ketua['siswa']->id);
        $milikAnggota = $this->buatNotifikasiSiswa($anggota->id);

        $this->actingAs($ketua['user'])
            ->delete(route('ketua.notifikasi.clear-all'))
            ->assertRedirect();

        $this->assertDatabaseMissing('notifikasis', ['id' => $milikKetua->id]);
        $this->assertDatabaseHas('notifikasis', ['id' => $milikAnggota->id]);
    }

    public function test_clear_all_kesiswaan_hanya_notifikasi_user_sendiri(): void
    {
        $user = $this->makeUser('kesiswaan');
        $userLain = $this->makeUser('kesiswaan');

        $milikSendiri = $this->buatNotifikasiUser($user->id);
        $milikOrangLain = $this->buatNotifikasiUser($userLain->id);

        $this->actingAs($user)
            ->delete(route('kesiswaan.notifikasi.clear-all'))
            ->assertRedirect();

        $this->assertDatabaseMissing('notifikasis', ['id' => $milikSendiri->id]);
        $this->assertDatabaseHas('notifikasis', ['id' => $milikOrangLain->id]);
    }

    public function test_pembina_dapat_menghapus_dan_clear_all_terbatas_pada_miliknya(): void
    {
        $user = $this->makeUser('pembina');
        $pembina = Pembina::create([
            'nip' => 'NIP'.fake()->unique()->numerify('########'),
            'user_id' => $user->id,
            'nama' => fake()->name(),
            'jenis_kelamin' => 'laki-laki',
        ]);
        $userLain = $this->makeUser('pembina');
        $pembinaLain = Pembina::create([
            'nip' => 'NIP'.fake()->unique()->numerify('########'),
            'user_id' => $userLain->id,
            'nama' => fake()->name(),
            'jenis_kelamin' => 'laki-laki',
        ]);

        $notif = $this->buatNotifikasiPembina($pembina->id);
        $notifLain = $this->buatNotifikasiPembina($pembinaLain->id);

        $this->actingAs($user)
            ->delete(route('pembina.notifikasi.destroy', $notif))
            ->assertRedirect();

        $this->assertDatabaseMissing('notifikasis', ['id' => $notif->id]);

        $this->actingAs($user)
            ->delete(route('pembina.notifikasi.destroy', $notifLain))
            ->assertStatus(403);

        $notifBaru = $this->buatNotifikasiPembina($pembina->id);

        $this->actingAs($user)
            ->delete(route('pembina.notifikasi.clear-all'))
            ->assertRedirect();

        $this->assertDatabaseMissing('notifikasis', ['id' => $notifBaru->id]);
        $this->assertDatabaseHas('notifikasis', ['id' => $notifLain->id]);
    }

    public function test_halaman_notifikasi_semua_role_menampilkan_tombol_hapus(): void
    {
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));
        $this->buatNotifikasiSiswa($siswa->id);
        $this->actingAs($siswa->user)
            ->get(route('siswa.notifikasi'))
            ->assertOk()
            ->assertSee('Hapus Semua');

        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);
        // Halaman ketua hanya menampilkan notifikasi yang terkait pendaftaran ekskulnya.
        Notifikasi::create([
            'siswa_id' => $ketua['siswa']->id,
            'pendaftaran_id' => $ketua['siswa']->pendaftarans()->first()->id,
            'judul' => 'Pendaftaran Baru Masuk',
            'pesan' => 'Isi notifikasi untuk pengujian.',
        ]);
        $this->actingAs($ketua['user'])
            ->get(route('ketua.notifikasi'))
            ->assertOk()
            ->assertSee('Hapus Semua');

        $userPembina = $this->makeUser('pembina');
        $pembinaBaru = Pembina::create([
            'nip' => 'NIP'.fake()->unique()->numerify('########'),
            'user_id' => $userPembina->id,
            'nama' => fake()->name(),
            'jenis_kelamin' => 'laki-laki',
        ]);
        $this->buatNotifikasiPembina($pembinaBaru->id);
        $this->actingAs($userPembina)
            ->get(route('pembina.notifikasi'))
            ->assertOk()
            ->assertSee('Hapus Semua');

        $userKesiswaan = $this->makeUser('kesiswaan');
        $this->buatNotifikasiUser($userKesiswaan->id);
        $this->actingAs($userKesiswaan)
            ->get(route('kesiswaan.notifikasi'))
            ->assertOk()
            ->assertSee('Hapus Semua');
    }

    private function buatNotifikasiSiswa(int $siswaId, string $judul = 'Pengumuman'): Notifikasi
    {
        return Notifikasi::create([
            'siswa_id' => $siswaId,
            'judul' => $judul,
            'pesan' => 'Isi notifikasi untuk pengujian.',
        ]);
    }

    private function buatNotifikasiUser(int $userId): Notifikasi
    {
        return Notifikasi::create([
            'user_id' => $userId,
            'judul' => 'Pengumuman',
            'pesan' => 'Isi notifikasi untuk pengujian.',
        ]);
    }

    private function buatNotifikasiPembina(int $pembinaId): Notifikasi
    {
        return Notifikasi::create([
            'pembina_id' => $pembinaId,
            'judul' => 'Pengumuman',
            'pesan' => 'Isi notifikasi untuk pengujian.',
        ]);
    }
}
