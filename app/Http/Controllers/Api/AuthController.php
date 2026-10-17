<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Models\Pembina;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends ApiController
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'identifier' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ], [
            'identifier.required' => 'Nama pengguna wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $identifier = $validated['identifier'];
        $user = $this->resolveUser($identifier);

        if (! $user || ! $user->is_active || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'identifier' => 'Kredensial yang kamu masukkan tidak cocok.',
            ]);
        }

        $token = $user->createToken($validated['device_name'] ?? 'soul-mobile');

        return $this->ok([
            'token' => $token->plainTextToken,
            'user' => (new UserResource($user))->resolve(),
        ], 'Login berhasil.');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->noContent('Logout berhasil.');
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        Password::sendResetLink(['email' => $validated['email']]);

        return $this->ok(
            [],
            'Jika email terdaftar, tautan untuk mengatur ulang kata sandi akan dikirim.',
        );
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->loadMissing(['siswa.kelas', 'pembina']);

        return $this->ok([
            'user' => (new UserResource($user))->resolve(),
        ]);
    }

    public function setupOnboarding(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless(
            in_array($user->role, ['siswa', 'pembina'], true) &&
                $user->needsOnboarding(),
            403,
            'Pengaturan akun awal tidak diperlukan.',
        );

        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:8'],
            'password_confirmation' => [
                'nullable',
                'required_with:password',
                'same:password',
            ],
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_).',
            'username.min' => 'Username minimal 3 karakter.',
            'username.unique' => 'Username tersebut sudah digunakan. Silakan pilih username lain.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password_confirmation.required_with' => 'Konfirmasi password wajib diisi.',
            'password_confirmation.same' => 'Konfirmasi password tidak sama.',
        ]);

        $updates = ['username' => $validated['username']];
        if (! empty($validated['password'])) {
            $updates['password'] = Hash::make($validated['password']);
            $updates['onboarding_completed_at'] = now();
        }
        $user->update($updates);
        $user->refresh()->loadMissing(['siswa.kelas', 'pembina']);

        return $this->ok(
            [
                'user' => (new UserResource($user))->resolve(),
                'password_changed' => ! empty($validated['password']),
            ],
            'Pengaturan akun berhasil disimpan.',
        );
    }

    private function resolveUser(string $identifier): ?User
    {
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $identifier)->first();

            if ($user) {
                return $user;
            }
        }

        $user = User::where('username', $identifier)->first();

        if ($user) {
            return $user;
        }

        $userId = Siswa::where('nis', $identifier)->value('user_id')
            ?? Pembina::where('nip', $identifier)->value('user_id');

        return $userId ? User::find($userId) : null;
    }
}
