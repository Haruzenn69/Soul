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

#[Fillable(['username', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function siswa(): HasOne
    {
        return $this->hasOne(Siswa::class);
    }

    public function pembina(): HasOne
    {
        return $this->hasOne(Pembina::class);
    }

    public function needsProfileCompletion(): bool
    {
        if ($this->role === 'siswa') {
            return ! ($this->siswa?->isProfileComplete() ?? false);
        }

        if ($this->role === 'pembina') {
            return ! ($this->pembina?->isProfileComplete() ?? false);
        }

        return false;
    }

    public function needsOnboarding(): bool
    {
        return $this->role !== 'admin' && $this->role !== 'kesiswaan' && is_null($this->onboarding_completed_at);
    }

    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'onboarding_completed_at',
    ];
}
