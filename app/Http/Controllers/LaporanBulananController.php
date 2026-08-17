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
        $laporans = LaporanBulanan::where('ekskul_id', $ekskul->id)->get();
        return view('ketua.laporan-bulanan.index', compact('laporans'));
    }

    public function create()
    {
        $ekskul = $this->getEkskul();
        return view('ketua.laporan-bulanan.create', compact('ekskul'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bulan'           => 'required|string|max:7',
            'materi_kegiatan' => 'nullable|string|max:255',
            'ringkasan'       => 'nullable|string',
            'dokumentasi'     => 'nullable|string',
        ]);

        $ekskul = $this->getEkskul();

        LaporanBulanan::create([
            'ekskul_id'       => $ekskul->id,
            'bulan'           => $validated['bulan'],
            'materi_kegiatan' => $validated['materi_kegiatan'] ?? null,
            'ringkasan'       => $validated['ringkasan'] ?? null,
            'dokumentasi'     => $validated['dokumentasi'] ?? null,
            'status'          => 'draft',
        ]);

        return redirect()->route('ketua.laporan-bulanan.index')->with('success', 'Laporan bulanan berhasil dibuat.');
    }

    public function show(LaporanBulanan $laporanBulanan)
    {
        $laporanBulanan->load('ekskul');
        return view('ketua.laporan-bulanan.show', compact('laporanBulanan'));
    }

    public function update(Request $request, LaporanBulanan $laporanBulanan)
    {
        $validated = $request->validate([
            'materi_kegiatan' => 'nullable|string|max:255',
            'ringkasan'       => 'nullable|string',
            'dokumentasi'     => 'nullable|string',
        ]);

        $laporanBulanan->update($validated);

        return redirect()->route('ketua.laporan-bulanan.index')->with('success', 'Laporan bulanan berhasil diupdate.');
    }
}
