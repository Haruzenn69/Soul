<?php

namespace Tests\Feature;

use App\Models\Pendaftaran;
use App\Models\RiwayatPendaftaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesFixtures;
use Tests\TestCase;

class KetuaDirectMembershipApiTest extends TestCase
{
    use CreatesFixtures, RefreshDatabase;

    public function test_ketua_can_search_students_and_add_them_directly(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('xi'));
        $siswa->update(['nama' => 'Alya Calon']);

        $this->actingAs($ketua['user'])
            ->getJson('/api/ketua/anggota/calon-siswa?q=Alya')
            ->assertOk()
            ->assertJsonPath('data.total', 1)
            ->assertJsonPath('data.items.0.id', $siswa->id)
            ->assertJsonPath('data.items.0.sudah_aktif', false);

        $this->actingAs($ketua['user'])
            ->postJson('/api/ketua/anggota', ['siswa_ids' => [$siswa->id]])
            ->assertOk()
            ->assertJsonPath('message', 'Berhasil menambahkan 1 siswa ke ekstrakurikuler.');

        $pendaftaran = Pendaftaran::where('siswa_id', $siswa->id)
            ->where('ekskul_id', $ekskul->id)
            ->firstOrFail();
        $this->assertSame(Pendaftaran::STATUS_DITERIMA, $pendaftaran->status);
        $this->assertSame('anggota', $siswa->fresh()->jabatan);
        $this->assertDatabaseHas('notifikasis', [
            'siswa_id' => $siswa->id,
            'pendaftaran_id' => $pendaftaran->id,
            'judul' => 'Ditambahkan ke Ekskul',
        ]);
    }

    public function test_ketua_can_reactivate_former_member_through_mobile_api(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('xi'));
        $existing = $this->daftarkan($siswa, $ekskul, Pendaftaran::STATUS_KELUAR);

        $this->actingAs($ketua['user'])
            ->postJson('/api/ketua/anggota', ['siswa_ids' => [$siswa->id]])
            ->assertOk();

        $this->assertSame(Pendaftaran::STATUS_DITERIMA, $existing->fresh()->status);
        $this->assertDatabaseHas('riwayat_pendaftarans', [
            'pendaftaran_id' => $existing->id,
            'status' => Pendaftaran::STATUS_KELUAR,
        ]);
        $this->assertInstanceOf(
            RiwayatPendaftaran::class,
            RiwayatPendaftaran::where('pendaftaran_id', $existing->id)->first(),
        );
    }

    public function test_non_ketua_cannot_add_students_through_mobile_api(): void
    {
        $ekskul = $this->makeEkskul();
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $this->makeKelas('x'));

        $this->actingAs($siswa->user)
            ->postJson('/api/ketua/anggota', ['siswa_ids' => [$siswa->id]])
            ->assertForbidden();

        $this->assertDatabaseMissing('pendaftarans', [
            'siswa_id' => $siswa->id,
            'ekskul_id' => $ekskul->id,
        ]);
    }
}
