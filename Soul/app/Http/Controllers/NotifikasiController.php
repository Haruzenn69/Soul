<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function index()
    {
        $siswa = auth()->user()->siswa;

        $notifikasis = $siswa
            ? $siswa->notifikasis()->with('pendaftaran.ekskul')->latest()->get()
            : collect();

        $unreadCount = $siswa ? $siswa->notifikasis()->where('is_read', false)->count() : 0;

        return view('siswa.notifikasi', compact('notifikasis', 'unreadCount', 'siswa'));
    }

    public function read(Notifikasi $notifikasi)
    {
        abort_unless($notifikasi->siswa_id === auth()->user()->siswa?->id, 403);

        if (!$notifikasi->is_read) {
            $notifikasi->update(['is_read' => true]);
        }

        return redirect()->back();
    }

    public function readAll()
    {
        $siswa = auth()->user()->siswa;

        if ($siswa) {
            $siswa->notifikasis()->where('is_read', false)->update(['is_read' => true]);
        }

        return redirect()->back();
    }
}