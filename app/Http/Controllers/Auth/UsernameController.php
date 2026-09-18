<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UsernameController extends Controller
{
    public function create(): RedirectResponse
    {
        // Tidak ada halaman khusus lagi — username diisi lewat modal di dashboard.
        return redirect($this->home(auth()->user()));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'username' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'regex:/^[a-zA-Z0-9._]+$/',
                'unique:users,username,'.$user->id,
            ],
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.min'      => 'Username minimal 3 karakter.',
            'username.regex'    => 'Username hanya boleh huruf, angka, titik, dan underscore.',
            'username.unique'   => 'Username sudah dipakai, pilih yang lain.',
        ]);

        $user->update(['username' => $request->input('username')]);

        return redirect($this->home($user->refresh()));
    }

    /**
     * Arahkan kembali ke beranda sesuai role/jabatan.
     */
    private function home(User $user): string
    {
        if ($user->role === 'siswa') {
            if ($user->siswa && $user->siswa->jabatan === 'ketua') {
                return route('ketua.dashboard');
            }

            return route('siswa.dashboard');
        }

        if ($user->role === 'pembina') {
            return route('pembina.dashboard');
        }

        return route('dashboard');
    }
}