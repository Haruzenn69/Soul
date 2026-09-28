<?php

namespace App\Http\Requests\Auth;

use App\Models\Pembina;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $identifier = $this->input('email');
        $password = $this->input('password');
        $remember = $this->boolean('remember');

        $attempted = false;

        // 1. Coba sebagai email jika formatnya valid
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $attempted = Auth::attempt(['email' => $identifier, 'password' => $password], $remember);
        }

        // 2. Coba sebagai username
        if (! $attempted) {
            $attempted = Auth::attempt(['username' => $identifier, 'password' => $password], $remember);
        }

        // 3. Coba sebagai NIS (siswa) atau NIP (pembina)
        if (! $attempted) {
            $userId = Siswa::query()->where('nis', $identifier)->value('user_id')
                ?? Pembina::query()->where('nip', $identifier)->value('user_id');

            if ($userId) {
                $user = User::find($userId);

                if ($user) {
                    $attempted = Auth::attempt(['username' => $user->username, 'password' => $password], $remember);
                }
            }
        }

        if (! $attempted) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
