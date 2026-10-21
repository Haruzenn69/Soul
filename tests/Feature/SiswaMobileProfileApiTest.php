<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\PengajuanKeluar;
use Tests\Support\CreatesFixtures;
use Tests\TestCase;

class SiswaMobileProfileApiTest extends TestCase
{
    use CreatesFixtures, RefreshDatabase;

    public function test_profile_photo_url_is_included_in_mobile_user_resources(): void
    {
        config(['app.url' => 'http://soul.test']);
        $user = $this->makeUser('siswa');
        $siswa = $this->makeSiswa($user, $this->makeKelas('x'));
        $siswa->update(['foto' => 'profile-photos/student.jpg']);

        $this->actingAs($user)
            ->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath(
                'data.user.siswa.foto',
                'http://soul.test/storage/profile-photos/student.jpg',
            );

        $this->actingAs($user)
            ->getJson('/api/siswa/profile')
            ->assertOk()
            ->assertJsonPath(
                'data.siswa.foto',
                'http://soul.test/storage/profile-photos/student.jpg',
            );
    }

    public function test_ketua_membership_endpoints_include_student_photo_urls(): void
    {
            config(['app.url' => 'http://soul.test']);
            $ekskul = $this->makeEkskul();
            $ketua = $this->makeKetua($ekskul);
            $memberUser = $this->makeUser('siswa');
            $member = $this->makeSiswa($memberUser, $this->makeKelas('x'));
            $member->update([
                'nama' => 'Foto Anggota',
                'foto' => 'profile-photos/member.jpg',
            ]);
            $this->daftarkan($member, $ekskul);

            $pendingUser = $this->makeUser('siswa');
            $pending = $this->makeSiswa($pendingUser, $this->makeKelas('x'));
            $pending->update([
                'nama' => 'Foto Pendaftar',
                'foto' => 'profile-photos/pending.jpg',
            ]);
            $this->daftarkan($pending, $ekskul, 'pending');
            PengajuanKeluar::create([
                'siswa_id' => $member->id,
                'ekskul_id' => $ekskul->id,
                'alasan' => 'Saya ingin fokus ke pelajaran utama.',
                'status' => 'pending',
                'tanggal_pengajuan' => now()->toDateString(),
            ]);

            $this->actingAs($ketua['user'])
                ->getJson('/api/ketua/anggota?cari=Foto%20Anggota')
                ->assertOk()
                ->assertJsonPath(
                    'data.anggotas.0.siswa.foto',
                    'http://soul.test/storage/profile-photos/member.jpg',
                );

            $this->actingAs($ketua['user'])
                ->getJson('/api/ketua/pendaftaran?cari=Foto%20Pendaftar')
                ->assertOk()
                ->assertJsonPath(
                    'data.pendaftarans.0.siswa.foto',
                    'http://soul.test/storage/profile-photos/pending.jpg',
                );

            $this->actingAs($ketua['user'])
                ->getJson('/api/ketua/pengajuan-keluar?cari=Foto%20Anggota')
                ->assertOk()
                ->assertJsonPath(
                    'data.pengajuans.0.foto',
                    'http://soul.test/storage/profile-photos/member.jpg',
                );
    }

    public function test_siswa_can_update_all_profile_fields_and_password_from_mobile(): void
    {
        $user = $this->makeUser('siswa', ['email' => 'old@example.test']);
        $siswa = $this->makeSiswa($user, $this->makeKelas('x'));

        $this->actingAs($user)
            ->postJson('/api/siswa/profile', [
                'username' => 'updated_student',
                'email' => 'new@example.test',
                'jenis_kelamin' => 'perempuan',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '2008-04-15 00:00:00',
                'agama' => 'Islam',
                'angkatan' => '2024',
                'no_telp' => '08123456789',
                'alamat' => 'Jalan Contoh',
                'medsos' => '@student',
            ])
            ->assertOk();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'username' => 'updated_student',
            'email' => 'new@example.test',
        ]);
        $this->assertDatabaseHas('siswas', [
            'id' => $siswa->id,
            'jenis_kelamin' => 'perempuan',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2008-04-15 00:00:00',
            'agama' => 'Islam',
            'angkatan' => '2024',
            'no_telp' => '08123456789',
            'alamat' => 'Jalan Contoh',
            'medsos' => '@student',
        ]);

        $this->actingAs($user)
            ->postJson('/api/siswa/password', [
                'current_password' => 'password',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])
            ->assertOk();

        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));
    }
}
