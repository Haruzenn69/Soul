<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KetuaEkskulMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Kesiswaan dan Admin memiliki akses penuh ke fitur ketua untuk seluruh ekskul
        if ($user && in_array($user->role, ['kesiswaan', 'admin'], true)) {
            return $next($request);
        }

        if ($user->role !== 'siswa') {
            abort(403, 'Akses ditolak.');
        }

        if (!$user->siswa || $user->siswa->jabatan !== 'ketua') {
            abort(403, 'Anda bukan ketua ekstrakurikuler.');
        }

        return $next($request);
    }
}