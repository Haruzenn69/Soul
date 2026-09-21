<?php

namespace Tests\Feature;

use App\Models\Pendaftaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesFixtures;
use Tests\TestCase;

class KesiswaanEkskulTest extends TestCase
{
    use CreatesFixtures, RefreshDatabase;

    public function test_admin_dapat_menonaktifkan_ekskul(): void
    {
        $admin = $this->makeUser('admin');
        $ekskul = $this->makeEkskul(['status' => true]);

        $this->actingAs($admin)
            ->delete('/kesiswaan/ekskuls/'.$ekskul->id)
            ->assertRedirect();

        $this->assertDatabaseHas('ekskuls', [
            'id' => $ekskul->id,
            'status' => false,
        ]);
    }

    public function test_admin_tidak_bisa_menetapkan_dua_ketua_pada_satu_ekskul(): void
    {
        $admin = $this->makeUser('admin');
        $ekskul = $this->makeEkskul();
        $this->makeKetua($ekskul);
        $kelas = $this->makeKelas('xi');

        $this->actingAs($admin)
            ->post('/kesiswaan/users', [
                'username' => 'ketua-baru',
                'email' => 'ketua-baru@example.test',
                'role' => 'siswa',
                'nis' => '9876543210',
                'nama' => 'Ketua Baru',
                'kelas_id' => $kelas->id,
                'jenis_kelamin' => 'laki-laki',
                'jabatan' => 'ketua',
                'ekskul_id' => $ekskul->id,
            ])
            ->assertStatus(422);

        $this->assertSame(1, Pendaftaran::query()
            ->where('ekskul_id', $ekskul->id)
            ->where('status', Pendaftaran::STATUS_DITERIMA)
            ->whereHas('siswa', fn ($query) => $query->where('jabatan', 'ketua'))
            ->count());
    }
}
