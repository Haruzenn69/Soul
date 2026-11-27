<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Pengurus;
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

        $siswa = $user->siswa;
        $isKetuaUtama = $siswa && $siswa->jabatan === 'ketua' && $siswa->ekskulDipimpin();
        $isKetuaSubdivisi = $siswa && Pengurus::query()
            ->where('siswa_id', $siswa->id)
            ->where('jabatan', Pengurus::JABATAN_WAKIL_KETUA)
            ->where('status', Pengurus::STATUS_AKTIF)
            ->whereNotNull('subdivisi_id')
            ->whereHas('subdivisi', fn ($query) => $query->whereNotNull('parent_id'))
            ->exists();

        if (! $isKetuaUtama && ! $isKetuaSubdivisi) {
            abort(403, 'Anda bukan ketua ekstrakurikuler.');
        }

        if (! $isKetuaUtama) {
            view()->share('isKetuaSubdivisiOnly', true);
            abort_unless($request->routeIs(
                'ketua.kegiatan.*',
                'ketua.presensi.*',
                'ketua.laporan-bulanan.*',
                'ketua.anggota.*',
                'ketua.pendaftaran.*',
                'api.ketua.kegiatan.*',
                'api.ketua.laporan-bulanan.*',
                'api.ketua.anggota*',
                'api.ketua.pendaftaran*',
            ), 403, 'Ketua sub divisi hanya dapat mengelola kegiatan, presensi, dan laporan sub divisnya.');
        } else {
            view()->share('isKetuaSubdivisiOnly', false);
        }

        return $next($request);
    }
}
