<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Ekskul;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $siswa = $user->siswa;

        $isRegistered = false;
        $isPending = false;

        if ($siswa) {
            $isRegistered = $siswa->activePendaftaran() ? true : false;
            $isPending = $siswa->pendingPendaftaran() ? true : false;
        }

        $ekskuls = Ekskul::with('pembina')
            ->when($request->filled('cari'), function ($query) use ($request) {
                $cari = $request->input('cari');
                $query->where(function ($sub) use ($cari) {
                    $sub->where('nama_ekskul', 'like', "%{$cari}%")
                        ->orWhere('deskripsi', 'like', "%{$cari}%")
                        ->orWhereHas('pembina', fn ($p) => $p->where('nama', 'like', "%{$cari}%"));
                });
            })
            ->get();

        return view('siswa.katalog', compact('ekskuls', 'siswa', 'isRegistered', 'isPending'));
    }
}
