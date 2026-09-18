<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUsernameIsSet
{
    /**
     * Minta pengguna (selain kesiswaan/admin) melengkapi username.
     * Halaman dashboard dibiarkan tampil karena modal username
     * dirender di dashboard (siswa/ketua/pembina). Halaman lain
     * diarahkan ke dashboard sampai username diisi.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && !in_array($user->role, ['admin', 'kesiswaan'], true) && !$user->username) {
            $routeName = $request->route()?->getName();
            $isDashboard = in_array($routeName, ['dashboard', 'siswa.dashboard', 'ketua.dashboard', 'pembina.dashboard'], true);

            if (!$isDashboard) {
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}