<?php

namespace Tests\Feature;

use App\Models\PengajuanKeluar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesFixtures;
use Tests\TestCase;

class SiswaMultiEkskulApiTest extends TestCase
{
    use CreatesFixtures, RefreshDatabase;

    public function test_dashboard_and_catalog_include_every_active_membership(): void
    {
        $basket = $this->makeEkskul(['nama_ekskul' => 'Basket']);
        $futsal = $this->makeEkskul(['nama_ekskul' => 'Futsal']);
        $third = $this->makeEkskul(['nama_ekskul' => 'Tari']);
        $user = $this->makeUser('siswa');
        $siswa = $this->makeSiswa($user, $this->makeKelas('xii'));
        $this->daftarkan($siswa, $basket);
        $this->daftarkan($siswa, $futsal);

        $this->actingAs($user)
            ->getJson('/api/siswa/dashboard')
            ->assertOk()
            ->assertJsonCount(2, 'data.active_ekskuls')
            ->assertJsonCount(2, 'data.active_summaries')
            ->assertJsonPath('data.total_hadir', 0);

        $this->actingAs($user)
            ->getJson('/api/siswa/katalog')
            ->assertOk()
            ->assertJsonCount(2, 'data.active_pendaftarans')
            ->assertJsonCount(0, 'data.pending_pendaftarans')
            ->assertJsonPath(
                'data.alasan_per_ekskul.'.$third->id,
                'Kamu sudah terdaftar di 2 ekskul. Maksimal 2 ekskul per siswa.',
            );
    }

    public function test_student_activity_endpoints_select_a_membership_by_ekskul_id(): void
    {
        $basket = $this->makeEkskul(['nama_ekskul' => 'Basket']);
        $futsal = $this->makeEkskul(['nama_ekskul' => 'Futsal']);
        $user = $this->makeUser('siswa');
        $siswa = $this->makeSiswa($user, $this->makeKelas('xii'));
        $this->daftarkan($siswa, $basket);
        $this->daftarkan($siswa, $futsal);

        foreach ([
            'presensi' => 'data.pendaftaran.ekskul.id',
            'rekap' => 'data.ekskul.id',
            'nilai' => 'data.pendaftaran.ekskul.id',
            'pengajuan-keluar' => 'data.ekskul.id',
        ] as $endpoint => $path) {
            $this->actingAs($user)
                ->getJson("/api/siswa/{$endpoint}?ekskul_id={$futsal->id}")
                ->assertOk()
                ->assertJsonPath($path, $futsal->id)
                ->assertJsonCount(2, 'data.active_ekskuls');
        }
    }

    public function test_leave_request_is_created_for_the_selected_membership_only(): void
    {
        $basket = $this->makeEkskul(['nama_ekskul' => 'Basket']);
        $futsal = $this->makeEkskul(['nama_ekskul' => 'Futsal']);
        $user = $this->makeUser('siswa');
        $siswa = $this->makeSiswa($user, $this->makeKelas('xii'));
        $this->daftarkan($siswa, $basket);
        $this->daftarkan($siswa, $futsal);

        $this->actingAs($user)
            ->postJson('/api/siswa/pengajuan-keluar', [
                'ekskul_id' => $futsal->id,
                'alasan' => 'Saya ingin lebih fokus pada kegiatan akademik semester ini.',
            ])
            ->assertCreated();

        $this->assertDatabaseHas('pengajuan_keluars', [
            'siswa_id' => $siswa->id,
            'ekskul_id' => $futsal->id,
            'status' => PengajuanKeluar::STATUS_PENDING,
        ]);
        $this->assertDatabaseMissing('pengajuan_keluars', [
            'siswa_id' => $siswa->id,
            'ekskul_id' => $basket->id,
        ]);
    }
}
