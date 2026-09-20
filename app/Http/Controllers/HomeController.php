<?php

namespace App\Http\Controllers;

use App\Models\Ekskul;

class HomeController extends Controller
{
    public function landing()
    {
        $ekskuls = Ekskul::with('pembina')->get();

        return view('welcome', compact('ekskuls'));
    }

    public function dashboard()
    {
        $user = auth()->user();

        if ($user->role === 'kesiswaan' || $user->role === 'admin') {
            return redirect()->route('kesiswaan.dashboard');
        }

        if ($user->role === 'pembina') {
            return redirect()->route('pembina.dashboard');
        }

        if ($user->siswa && $user->siswa->isKetua()) {
            return redirect()->route('ketua.dashboard');
        }

        return redirect()->route('siswa.dashboard');
    }
}
