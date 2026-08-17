<?php

namespace App\Http\Controllers;

use App\Models\LaporanBulanan;
use App\Models\Ekskul;
use Illuminate\Http\Request;

class LaporanBulananController extends Controller
{
    public function index()
    {
        $laporans = LaporanBulanan::with('ekskul')->get();
        return view('pembina.laporan-bulanan.index', compact('laporans'));
    }

    public function create()
    {
        $ekskul = auth()->user()->pembina->ekskuls->first();
        return view('ketua.laporan-bulanan.create', compact('ekskul'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bulan'            => 'required|string|max:7',
            'materi_kegiatan'  => 'nullable|string|max:255',
            'ringkasan'        => 'nullable|string',
            'dokumentasi'      => 'nullable|string',
        ]);

        $ekskul = auth()->user()->siswa->pendaftarans()->where('status', 'diterima')->first()->ekskul;

        LaporanBulanan::create([
            'ekskul_id'        => $ekskul->id,
            'bulan'            => $validated['bulan'],
            'materi_kegiatan'  => $validated['materi_kegiatan'] ?? null,
            'ringkasan'        => $validated['ringkasan'] ?? null,
            'dokumentasi'      => $validated['dokumentasi'] ?? null,
            'status'           => 'draft',
        ]);

        return redirect()->route('laporan-bulanan.index')->with('success', 'Laporan bulanan berhasil dibuat.');
    }

    public function show(LaporanBulanan $laporanBulanan)
    {
        $laporanBulanan->load('ekskul');
        return view('pembina.laporan-bulanan.show', compact('laporanBulanan'));
    }

    public function edit(LaporanBulanan $laporanBulanan)
    {
        return view('ketua.laporan-bulanan.edit', compact('laporanBulanan'));
    }

    public function update(Request $request, LaporanBulanan $laporanBulanan)
    {
        $validated = $request->validate([
            'materi_kegiatan'  => 'nullable|string|max:255',
            'ringkasan'        => 'nullable|string',
            'dokumentasi'      => 'nullable|string',
            'status'           => 'nullable|in:draft,disetujui,ditolak',
            'catatan_pembina'  => 'nullable|string',
        ]);

        $laporanBulanan->update($validated);

        return redirect()->route('laporan-bulanan.index')->with('success', 'Laporan bulanan berhasil diupdate.');
    }

    public function destroy(LaporanBulanan $laporanBulanan)
    {
        $laporanBulanan->delete();

        return redirect()->route('laporan-bulanan.index')->with('success', 'Laporan bulanan berhasil dihapus.');
    }
}