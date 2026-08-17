<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Kegiatan;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function index(Kegiatan $kegiatan)
    {
        $presensis = Presensi::where('kegiatan_id', $kegiatan->id)
            ->with('pendaftaran.siswa')
            ->get();

        return view('ketua.presensi.index', compact('presensis', 'kegiatan'));
    }

    public function create(Kegiatan $kegiatan)
    {
        $pendaftarans = Pendaftaran::where('ekskul_id', $kegiatan->ekskul_id)
            ->where('status', 'diterima')
            ->with('siswa')
            ->get();

        return view('ketua.presensi.create', compact('kegiatan', 'pendaftarans'));
    }

    public function store(Request $request, Kegiatan $kegiatan)
    {
        $validated = $request->validate([
            'presensi'   => 'required|array',
            'presensi.*.pendaftaran_id' => 'required|exists:pendaftarans,id',
            'presensi.*.status'         => 'required|in:hadir,sakit,izin,alpha',
            'dokumentasi'  => 'nullable|array',
        ]);

        foreach ($validated['presensi'] as $item) {
            Presensi::updateOrCreate(
                [
                    'kegiatan_id'      => $kegiatan->id,
                    'pendaftaran_id'   => $item['pendaftaran_id'],
                ],
                [
                    'status'       => $item['status'],
                    'dokumentasi'  => $validated['dokumentasi'] ?? null,
                ]
            );
        }

        return redirect()->route('kegiatan.show', $kegiatan)->with('success', 'Presensi berhasil disimpan.');
    }

    public function show(Presensi $presensi)
    {
        $presensi->load(['kegiatan', 'pendaftaran.siswa']);
        return view('ketua.presensi.show', compact('presensi'));
    }

    public function edit(Presensi $presensi)
    {
        return view('ketua.presensi.edit', compact('presensi'));
    }

    public function update(Request $request, Presensi $presensi)
    {
        $validated = $request->validate([
            'status'      => 'required|in:hadir,sakit,izin,alpha',
            'dokumentasi' => 'nullable|array',
        ]);

        $presensi->update($validated);

        return redirect()->route('kegiatan.show', $presensi->kegiatan)->with('success', 'Presensi berhasil diupdate.');
    }

    public function destroy(Presensi $presensi)
    {
        $presensi->delete();

        return redirect()->route('kegiatan.show', $presensi->kegiatan)->with('success', 'Presensi berhasil dihapus.');
    }
}