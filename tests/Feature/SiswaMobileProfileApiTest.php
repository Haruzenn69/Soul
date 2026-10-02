<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Support\CreatesFixtures;
use Tests\TestCase;

class SiswaMobileProfileApiTest extends TestCase
{
    use CreatesFixtures, RefreshDatabase;

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
