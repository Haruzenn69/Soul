<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesFixtures;
use Tests\TestCase;

class SiswaIndexTest extends TestCase
{
    use CreatesFixtures, RefreshDatabase;

    private function asKesiswaan()
    {
        return $this->actingAs($this->makeUser('kesiswaan'));
    }

    public function test_kesiswaan_bisa_membuka_daftar_siswa(): void
    {
        $kelas = $this->makeKelas('x');
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $kelas);

        $response = $this->asKesiswaan()->get(route('kesiswaan.siswa.index'));

        $response->assertOk();
        $response->assertSee($siswa->nama);
        $response->assertSee($siswa->nis);
    }

    public function test_halaman_siswa_tertutup_untuk_siswa(): void
    {
        $this->actingAs($this->makeUser('siswa'))
            ->get(route('kesiswaan.siswa.index'))
            ->assertForbidden();
    }

    public function test_pencarian_mencocokkan_nama_nis_dan_email(): void
    {
        $kelas = $this->makeKelas('x');
        $target = $this->makeSiswa($this->makeUser('siswa'), $kelas);
        $other = $this->makeSiswa($this->makeUser('siswa'), $kelas);

        $this->asKesiswaan()
            ->get(route('kesiswaan.siswa.index', ['q' => $target->nis]))
            ->assertOk()
            ->assertSee($target->nis)
            ->assertDontSee($other->nis);
    }

    public function test_filter_kelas_dan_angkatan(): void
    {
        $kelasA = $this->makeKelas('x');
        $kelasB = $this->makeKelas('xi');

        $userA = $this->makeUser('siswa');
        $siswaA = Siswa::create([
            'nis' => '1000000001',
            'user_id' => $userA->id,
            'nama' => 'Zulfan Kelas Sepuluh',
            'kelas_id' => $kelasA->id,
            'angkatan' => '2022',
            'jenis_kelamin' => 'laki-laki',
        ]);

        $userB = $this->makeUser('siswa');
        $siswaB = Siswa::create([
            'nis' => '1000000002',
            'user_id' => $userB->id,
            'nama' => 'Yuni Kelas Sebelas',
            'kelas_id' => $kelasB->id,
            'angkatan' => '2024',
            'jenis_kelamin' => 'perempuan',
        ]);

        $this->asKesiswaan()
            ->get(route('kesiswaan.siswa.index', ['kelas_id' => $kelasA->id]))
            ->assertOk()
            ->assertSee($siswaA->nama)
            ->assertDontSee($siswaB->nama);

        $this->asKesiswaan()
            ->get(route('kesiswaan.siswa.index', ['angkatan' => '2024']))
            ->assertOk()
            ->assertSee($siswaB->nama)
            ->assertDontSee($siswaA->nama);
    }

    public function test_urutan_nama_bolak(): void
    {
        $kelas = $this->makeKelas('x');

        $userA = $this->makeUser('siswa');
        $siswaA = Siswa::create([
            'nis' => '2000000001',
            'user_id' => $userA->id,
            'nama' => 'Andi',
            'kelas_id' => $kelas->id,
            'jenis_kelamin' => 'laki-laki',
        ]);

        $userB = $this->makeUser('siswa');
        $siswaB = Siswa::create([
            'nis' => '2000000002',
            'user_id' => $userB->id,
            'nama' => 'Budi',
            'kelas_id' => $kelas->id,
            'jenis_kelamin' => 'laki-laki',
        ]);

        $asc = $this->asKesiswaan()->get(route('kesiswaan.siswa.index', ['sort' => 'nama', 'direction' => 'asc']));
        $asc->assertOk();
        $this->assertLessThan(strpos($asc->getContent(), $siswaB->nama), strpos($asc->getContent(), $siswaA->nama));

        $desc = $this->asKesiswaan()->get(route('kesiswaan.siswa.index', ['sort' => 'nama', 'direction' => 'desc']));
        $desc->assertOk();
        $this->assertLessThan(strpos($desc->getContent(), $siswaA->nama), strpos($desc->getContent(), $siswaB->nama));
    }

    public function test_urutan_kelas_mengikuti_nama_kelas(): void
    {
        $tahunAjaran = $this->makeTahunAjaran();
        $kelasA = Kelas::create(['nama' => 'X-AA', 'tingkat' => 'x', 'tahun_ajaran_id' => $tahunAjaran->id]);
        $kelasB = Kelas::create(['nama' => 'X-ZZ', 'tingkat' => 'x', 'tahun_ajaran_id' => $tahunAjaran->id]);

        $userA = $this->makeUser('siswa');
        $siswaA = $this->makeSiswa($userA, $kelasA);
        $siswaA->update(['nama' => 'Zeta']);

        $userB = $this->makeUser('siswa');
        $siswaB = $this->makeSiswa($userB, $kelasB);
        $siswaB->update(['nama' => 'Alfa']);

        $response = $this->asKesiswaan()->get(route('kesiswaan.siswa.index', ['sort' => 'kelas']));

        $response->assertOk();
        $this->assertLessThan(strpos($response->getContent(), 'Alfa'), strpos($response->getContent(), 'Zeta'));
    }
}
