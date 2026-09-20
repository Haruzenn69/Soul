<?php

namespace Tests\Support;

use App\Models\Ekskul;
use App\Models\Kelas;
use App\Models\Pelatih;
use App\Models\Pembina;
use App\Models\Pendaftaran;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;

trait CreatesFixtures
{
    private function makeUser(string $role, array $overrides = []): User
    {
        return User::factory()->create(array_merge(['role' => $role], $overrides));
    }

    private function makeTahunAjaran(): TahunAjaran
    {
        return TahunAjaran::create(['nama' => 'TA'.fake()->unique()->numerify('####'), 'is_active' => true]);
    }

    private function makeKelas(string $tingkat): Kelas
    {
        return Kelas::create([
            'nama' => 'X-'.strtoupper($tingkat),
            'tingkat' => $tingkat,
            'tahun_ajaran_id' => $this->makeTahunAjaran()->id,
        ]);
    }

    private function makePembina(): Pembina
    {
        $user = $this->makeUser('pembina');

        return Pembina::create([
            'nip' => 'NIP'.fake()->unique()->numerify('########'),
            'user_id' => $user->id,
            'nama' => fake()->name(),
            'jenis_kelamin' => 'laki-laki',
        ]);
    }

    private function makePelatih(): Pelatih
    {
        return Pelatih::create([
            'nama' => fake()->name(),
            'jenis_kelamin' => 'laki-laki',
            'no_hp' => '08'.fake()->numerify('########'),
        ]);
    }

    private function makeEkskul(array $overrides = []): Ekskul
    {
        return Ekskul::create(array_merge([
            'pembina_id' => $this->makePembina()->id,
            'pelatih_id' => $this->makePelatih()->id,
            'nama_ekskul' => 'Ekskul '.fake()->unique()->word(),
            'deskripsi' => fake()->sentence(),
            'is_open_recruitment' => true,
        ], $overrides));
    }

    private function makeSiswa(User $user, Kelas $kelas, string $jabatan = 'siswa'): Siswa
    {
        return Siswa::create([
            'nis' => fake()->unique()->numerify('##########'),
            'user_id' => $user->id,
            'nama' => fake()->name(),
            'kelas_id' => $kelas->id,
            'jenis_kelamin' => 'laki-laki',
            'jabatan' => $jabatan,
        ]);
    }

    private function daftarkan(Siswa $siswa, Ekskul $ekskul, string $status = Pendaftaran::STATUS_DITERIMA): Pendaftaran
    {
        return Pendaftaran::create([
            'siswa_id' => $siswa->id,
            'ekskul_id' => $ekskul->id,
            'tanggal_daftar' => now()->toDateString(),
            'status' => $status,
            'alasan' => 'Saya ingin mengembangkan keterampilan dan pengalaman.',
        ]);
    }

    /**
     * @return array{user: User, siswa: Siswa}
     */
    private function makeKetua(Ekskul $ekskul): array
    {
        $user = $this->makeUser('siswa');
        $siswa = $this->makeSiswa($user, $this->makeKelas('x'), 'ketua');
        $this->daftarkan($siswa, $ekskul, Pendaftaran::STATUS_DITERIMA);

        return ['user' => $user, 'siswa' => $siswa];
    }
}
