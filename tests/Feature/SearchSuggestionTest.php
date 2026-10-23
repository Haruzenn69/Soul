<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesFixtures;
use Tests\TestCase;

class SearchSuggestionTest extends TestCase
{
    use CreatesFixtures, RefreshDatabase;

    public function test_endpoint_menampilkan_suggestion_siswa_untuk_kesiswaan(): void
    {
        $kelas = $this->makeKelas('x');
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $kelas);
        $siswa->update(['nama' => 'Zulfan Pratama']);

        $response = $this->actingAs($this->makeUser('kesiswaan'))
            ->getJson(route('search.suggestions', ['q' => 'Zulfan']));

        $response->assertOk();
        $response->assertJsonPath('query', 'Zulfan');

        $items = $response->json('items');
        $this->assertNotEmpty($items);

        $label = collect($items)->firstWhere('type', 'Siswa');
        $this->assertNotNull($label, 'Siswa yang cocok tidak ditemukan di suggestion.');
        $this->assertSame('ZULFAN PRATAMA', $label['label']);
        $this->assertStringContainsString($siswa->nis, $label['meta']);
        $this->assertNotEmpty($label['url']);
    }

    public function test_endpoint_mencocokkan_nis_siswa(): void
    {
        $kelas = $this->makeKelas('x');
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $kelas);

        $response = $this->actingAs($this->makeUser('kesiswaan'))
            ->getJson(route('search.suggestions', ['q' => $siswa->nis]));

        $response->assertOk();
        $this->assertNotEmpty($response->json('items'));
    }

    public function test_query_pendek_tidak_mengembalikan_hasil(): void
    {
        $response = $this->actingAs($this->makeUser('kesiswaan'))
            ->getJson(route('search.suggestions', ['q' => 'a']));

        $response->assertOk();
        $this->assertSame([], $response->json('items'));
    }

    public function test_endpoint_menampilkan_suggestion_ekskul_untuk_siswa(): void
    {
        $ekskul = $this->makeEkskul(['nama_ekskul' => 'Paskibra Nusantara']);

        $response = $this->actingAs($this->makeUser('siswa'))
            ->getJson(route('search.suggestions', ['q' => 'Paskibra']));

        $response->assertOk();
        $items = $response->json('items');
        $this->assertNotEmpty($items);
        $this->assertSame('Paskibra Nusantara', $items[0]['label']);
    }

    public function test_endpoint_menampilkan_suggestion_siswa_untuk_pembina(): void
    {
        $kelas = $this->makeKelas('x');
        $siswa = $this->makeSiswa($this->makeUser('siswa'), $kelas);
        $siswa->update(['nama' => 'Anggota Pembina']);

        $response = $this->actingAs($this->makeUser('pembina'))
            ->getJson(route('search.suggestions', ['q' => 'Anggota']));

        $response->assertOk();
        $this->assertNotEmpty($response->json('items'));
    }

    public function test_endpoint_menampilkan_suggestion_kelas(): void
    {
        $kelas = Kelas::create([
            'nama' => 'X-AA',
            'tingkat' => 'x',
            'tahun_ajaran_id' => $this->makeTahunAjaran()->id,
        ]);

        $response = $this->actingAs($this->makeUser('kesiswaan'))
            ->getJson(route('search.suggestions', ['q' => 'X-AA']));

        $response->assertOk();
        $items = $response->json('items');
        $this->assertNotEmpty($items);
        $this->assertSame('X-AA', $items[0]['label']);
        $this->assertSame('Kelas', $items[0]['type']);
    }

    public function test_endpoint_menolak_tamu(): void
    {
        $this->getJson(route('search.suggestions', ['q' => 'Zulfan']))->assertUnauthorized();
    }
}
