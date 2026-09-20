<?php

namespace Tests\Feature;

use App\Models\Pendaftaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesFixtures;
use Tests\TestCase;

class PendaftaranFlowTest extends TestCase
{
    use CreatesFixtures, RefreshDatabase;

    public function test_siswa_dapat_mendaftar_dan_notifikasi_terkirim_ke_ketua(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('xi'));

        $this->actingAs($siswa->user)
            ->post('/siswa/daftar-ekskul', [
                'ekskul_id' => $ekskul->id,
                'alasan' => 'Saya ingin mengembangkan keterampilan dan menambah pengalaman.',
            ])
            ->assertRedirect(route('siswa.dashboard'));

        $this->assertDatabaseHas('pendaftarans', [
            'siswa_id' => $siswa->id,
            'ekskul_id' => $ekskul->id,
            'status' => Pendaftaran::STATUS_PENDING,
        ]);

        $pendaftaran = Pendaftaran::where('siswa_id', $siswa->id)->first();

        $this->assertDatabaseHas('notifikasis', [
            'siswa_id' => $ketua['siswa']->id,
            'pendaftaran_id' => $pendaftaran->id,
            'judul' => 'Pendaftaran Baru Masuk',
        ]);
    }

    public function test_siswa_yang_sudah_terdaftar_tidak_bisa_mendaftar_lagi(): void
    {
        $ekskul = $this->makeEkskul();
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));
        $this->daftarkan($siswa, $ekskul, Pendaftaran::STATUS_DITERIMA);

        $before = Pendaftaran::count();

        $this->actingAs($siswa->user)
            ->post('/siswa/daftar-ekskul', [
                'ekskul_id' => $ekskul->id,
                'alasan' => 'Saya ingin mengembangkan keterampilan dan menambah pengalaman.',
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame($before, Pendaftaran::count());
    }

    public function test_alasan_iseng_ditolak_validasi(): void
    {
        $ekskul = $this->makeEkskul();
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));

        $this->actingAs($siswa->user)
            ->post('/siswa/daftar-ekskul', [
                'ekskul_id' => $ekskul->id,
                'alasan' => 'tes',
            ])
            ->assertSessionHasErrors('alasan');

        $this->assertSame(0, Pendaftaran::count());
    }

    public function test_ketua_menyetujui_pendaftaran_menjadikan_anggota_dan_bernotifikasi(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));
        $pendaftaran = $this->daftarkan($siswa, $ekskul, Pendaftaran::STATUS_PENDING);

        $this->actingAs($ketua['user'])
            ->patch('/ketua/pendaftaran/'.$pendaftaran->id, ['status' => Pendaftaran::STATUS_DITERIMA])
            ->assertRedirect(route('ketua.pendaftaran.index'));

        $this->assertSame(Pendaftaran::STATUS_DITERIMA, $pendaftaran->fresh()->status);
        $this->assertSame('anggota', $siswa->fresh()->jabatan);

        $this->assertDatabaseHas('notifikasis', [
            'siswa_id' => $siswa->id,
            'pendaftaran_id' => $pendaftaran->id,
            'tipe' => 'diterima',
            'judul' => 'Pendaftaran Diterima',
        ]);
    }
}
