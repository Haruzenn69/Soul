<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Support\CreatesFixtures;
use Tests\TestCase;

class ApiOnboardingTest extends TestCase
{
    use CreatesFixtures, RefreshDatabase;

    public function test_siswa_dapat_mengganti_username_dan_password_saat_onboarding(): void
    {
        $user = $this->makeUser('siswa');
        $this->makeSiswa($user, $this->makeKelas('x'));

        $this->actingAs($user)
            ->postJson('/api/auth/onboarding/setup', [
                'username' => 'student_new',
                'password' => 'secure-password-2026',
                'password_confirmation' => 'secure-password-2026',
            ])
            ->assertOk()
            ->assertJsonPath('data.user.username', 'student_new')
            ->assertJsonPath('data.user.needs_onboarding', false);

        $user->refresh();
        $this->assertSame('student_new', $user->username);
        $this->assertTrue(Hash::check('secure-password-2026', $user->password));
        $this->assertNotNull($user->onboarding_completed_at);
    }

    public function test_onboarding_setup_rejects_weak_password_and_duplicate_username(): void
    {
        $user = $this->makeUser('siswa');
        $this->makeSiswa($user, $this->makeKelas('x'));
        $existing = $this->makeUser('siswa', ['username' => 'taken_name']);

        $this->actingAs($user)
            ->postJson('/api/auth/onboarding/setup', [
                'username' => $existing->username,
                'password' => 'short',
                'password_confirmation' => 'mismatch',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['username', 'password']);
    }

    public function test_onboarding_setup_is_not_available_after_completion(): void
    {
        $user = $this->makeUser('siswa', [
            'username' => 'already_setup',
            'onboarding_completed_at' => now(),
        ]);
        $this->makeSiswa($user, $this->makeKelas('x'));

        $this->actingAs($user)
            ->postJson('/api/auth/onboarding/setup', [
                'username' => 'new_name',
                'password' => 'secure-password-2026',
                'password_confirmation' => 'secure-password-2026',
            ])
            ->assertForbidden();
    }

    public function test_siswa_dapat_melewati_ganti_password_dan_onboarding_muncul_lagi_di_login_berikutnya(): void
    {
        $originalPassword = 'password';
        $user = $this->makeUser('siswa', [
            'password' => Hash::make($originalPassword),
        ]);
        $this->makeSiswa($user, $this->makeKelas('x'));

        $this->actingAs($user)
            ->postJson('/api/auth/onboarding/setup', [
                'username' => 'student_skip_password',
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertOk()
            ->assertJsonPath('data.user.needs_onboarding', true)
            ->assertJsonPath('data.password_changed', false);

        $user->refresh();
        $this->assertSame('student_skip_password', $user->username);
        $this->assertNull($user->onboarding_completed_at);
        $this->assertTrue(Hash::check($originalPassword, $user->password));
    }
}
