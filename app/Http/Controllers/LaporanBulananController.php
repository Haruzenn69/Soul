<?php

namespace App\Http\Controllers;

use App\Models\LaporanBulanan;
use Illuminate\Http\Request;

class LaporanBulananController extends Controller
{
    private function getEkskul()
    {
        return auth()->user()->siswa->pendaftarans()->where('status', 'diterima')->first()->ekskul;
    }

    public function index()
    {
        $ekskul = $this->getEkskul();
        $laporans = LaporanBulanan::where('ekskul_id', $ekskul->id)
            ->latest('bulan')
            ->get();

        return view('ketua.laporan-bulanan.index', compact('laporans'));
    }

    public function create()
    {
        return view('ketua.laporan-bulanan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bulan' => 'required|string',
            'materi_kegiatan' => 'nullable|string',
            'ringkasan' => 'nullable|string',
            'dokumentasi' => 'nullable|string',
        ]);

        $ekskul = $this->getEkskul();

        LaporanBulanan::create([
            'ekskul_id' => $ekskul->id,
            'bulan' => $validated['bulan'],
            'materi_kegiatan' => $validated['materi_kegiatan'] ?? null,
            'ringkasan' => $validated['ringkasan'] ?? null,
            'dokumentasi' => $validated['dokumentasi'] ?? null,
            'status' => 'draft',
        ]);

        return redirect()->route('ketua.laporan-bulanan.index')->with('success', 'Laporan bulanan berhasil dibuat.');
    }

    public function show(LaporanBulanan $laporan_bulanan)
    {
        return view('ketua.laporan-bulanan.show', ['laporan' => $laporan_bulanan]);
    }
}
