<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    private function getEkskul()
    {
        $pendaftaran = auth()->user()->siswa?->pendaftarans()->where('status', 'diterima')->first();
        abort_unless($pendaftaran, 404, 'Anda belum tergabung dalam ekskul mana pun.');
        return $pendaftaran->ekskul;
    }

    public function index()
    {
        $ekskul = $this->getEkskul();
        $pendaftarans = Pendaftaran::where('ekskul_id', $ekskul->id)
            ->with('siswa')
            ->latest('tanggal_daftar')
            ->get();

        return view('ketua.pendaftaran.index', compact('pendaftarans'));
    }

    public function show(Pendaftaran $pendaftaran)
    {
        $pendaftaran->load(['siswa', 'ekskul']);
        return view('ketua.pendaftaran.show', compact('pendaftaran'));
    }

    public function update(Request $request, Pendaftaran $pendaftaran)
    {
        $validated = $request->validate([
            'status' => 'required|in:diterima,ditolak',
        ]);

        $pendaftaran->update($validated);

        $ekskul = $pendaftaran->ekskul;

        if ($validated['status'] === 'diterima') {
            $pendaftaran->siswa->update(['jabatan' => 'anggota']);

            Notifikasi::create([
                'siswa_id' => $pendaftaran->siswa_id,
                'pendaftaran_id' => $pendaftaran->id,
                'judul' => 'Pendaftaran Diterima',
                'pesan' => 'Selamat! Pendaftaran kamu ke ekskul ' . $ekskul->nama_ekskul . ' telah diterima oleh ketua ekskul.',
                'tipe' => 'diterima',
            ]);

            if ($ekskul->pembina) {
                Notifikasi::create([
                    'pembina_id' => $ekskul->pembina->id,
                    'pendaftaran_id' => $pendaftaran->id,
                    'judul' => 'Anggota Baru Diterima',
                    'pesan' => $pendaftaran->siswa->nama . ' telah diterima menjadi anggota ekskul ' . $ekskul->nama_ekskul . '.',
                    'tipe' => 'diterima',
                ]);
            }
        } else {
            Notifikasi::create([
                'siswa_id' => $pendaftaran->siswa_id,
                'pendaftaran_id' => $pendaftaran->id,
                'judul' => 'Pendaftaran Ditolak',
                'pesan' => 'Maaf, pendaftaran kamu ke ekskul ' . $ekskul->nama_ekskul . ' ditolak oleh ketua ekskul. Kamu dapat mencoba mendaftar ke ekskul lain.',
                'tipe' => 'ditolak',
            ]);

            if ($ekskul->pembina) {
                Notifikasi::create([
                    'pembina_id' => $ekskul->pembina->id,
                    'pendaftaran_id' => $pendaftaran->id,
                    'judul' => 'Pendaftaran Ditolak',
                    'pesan' => 'Pendaftaran ' . $pendaftaran->siswa->nama . ' ke ekskul ' . $ekskul->nama_ekskul . ' ditolak oleh ketua ekskul.',
                    'tipe' => 'ditolak',
                ]);
            }
        }

        return redirect()->route('ketua.pendaftaran.index')->with('success', 'Status pendaftaran berhasil diupdate.');
    }
}
