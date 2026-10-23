<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasis = Notifikasi::where('user_id', auth()->id())
            ->with('pendaftaran.ekskul')
            ->latest()
            ->get();

        return view('kesiswaan.notifikasi', ['notifikasis' => $notifikasis]);
    }

    public function read(Notifikasi $notifikasi)
    {
        abort_unless($notifikasi->user_id === auth()->id(), 403);

        if (! $notifikasi->is_read) {
            $notifikasi->update(['is_read' => true]);
        }

        return redirect()->back();
    }

    public function readAll()
    {
        Notifikasi::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return redirect()->back();
    }

    public function destroy(Notifikasi $notifikasi)
    {
        abort_unless($notifikasi->user_id === auth()->id(), 403);

        $notifikasi->delete();

        return redirect()->back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    public function clearAll()
    {
        Notifikasi::where('user_id', auth()->id())->delete();

        return redirect()->back()->with('success', 'Semua notifikasi berhasil dihapus.');
    }
}
