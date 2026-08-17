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

        if ($user->role !== 'siswa') {
            abort(403, 'Akses ditolak.');
        }

        if (!$user->siswa || $user->siswa->jabatan !== 'ketua') {
            abort(403, 'Anda bukan ketua ekstrakurikuler.');
        }

        return $next($request);
    }
}