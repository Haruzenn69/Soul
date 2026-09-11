<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use Illuminate\Http\Request;

class AnggotaController extends Controller
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
        $anggotas = Pendaftaran::where('ekskul_id', $ekskul->id)
            ->whereIn('status', ['diterima', 'nonaktif'])
            ->with('siswa.kelas')
            ->latest('tanggal_daftar')
            ->get();

        return view('ketua.anggota.index', compact('anggotas'));
    }

    public function toggle(Pendaftaran $pendaftaran)
    {
        abort_unless($pendaftaran->ekskul_id === $this->getEkskul()->id, 403);

        $newStatus = $pendaftaran->status === 'diterima' ? 'nonaktif' : 'diterima';
        $pendaftaran->update(['status' => $newStatus]);

        return back()->with('success', 'Status anggota berhasil diupdate.');
    }
}
