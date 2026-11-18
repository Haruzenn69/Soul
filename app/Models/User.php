<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['username', 'email', 'password', 'role', 'is_active', 'onboarding_completed_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public function siswa(): HasOne
    {
        return $this->hasOne(Siswa::class);
    }

    public function pembina(): HasOne
    {
        return $this->hasOne(Pembina::class);
    }

    public function getFotoUrlAttribute(): string
    {
        return $this->siswa?->foto_url ?? $this->pembina?->foto_url ?? asset('images/default.png');
    }

    public function needsProfileCompletion(): bool
    {
        if ($this->role === 'siswa') {
            return ! ($this->siswa?->isProfileComplete() ?? false);
        }

        if (in_array($this->role, ['pembina', 'kesiswaan'], true)) {
            return ! ($this->pembina?->isProfileComplete() ?? false);
        }

        return false;
    }

    public function needsOnboarding(): bool
    {
        // Onboarding dinonaktifkan — username dan password diubah via halaman Profil masing-masing.
        return false;
    }


    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'is_active',
        'onboarding_completed_at',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
