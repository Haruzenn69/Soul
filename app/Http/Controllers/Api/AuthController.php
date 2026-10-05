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
