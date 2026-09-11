<?php

namespace App\Http\Controllers;

use App\Models\Ekskul;

class EkskulCatalogController extends Controller
{
    public function show(Ekskul $ekskul)
    {
        $ekskul->load([
            'pembina',
            'pelatih',
            'kegiatans' => fn($q) => $q->latest('tanggal_kegiatan')->take(6),
            'prestasis',
            'testimoniss',
            'faqs',
            'pendaftarans' => fn($q) => $q->where('status', 'diterima'),
        ]);

        $totalAnggota = $ekskul->pendaftarans->count();
        $galeris = $ekskul->kegiatans->pluck('dokumentasi')->filter()->values();

        return view('ekskul.detail', compact('ekskul', 'totalAnggota', 'galeris'));
    }
}