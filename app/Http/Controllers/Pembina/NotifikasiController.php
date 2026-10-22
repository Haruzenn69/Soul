<?php

namespace App\Http\Controllers\Pembina;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;

class NotifikasiController extends Controller
{
    public function index()
    {
        $pembina = auth()->user()?->pembina;

        $notifikasis = $pembina
            ? $pembina->notifikasis()->with('pendaftaran.ekskul')->latest()->get()
            : collect();

        return view('pembina.notifikasi', ['notifikasis' => $notifikasis]);
    }

    public function read(Notifikasi $notifikasi)
    {
        abort_unless($notifikasi->pembina_id === auth()->user()?->pembina?->id, 403);

        if (!$notifikasi->is_read) {
            $notifikasi->update(['is_read' => true]);
        }

        return redirect()->back();
    }

    public function readAll()
    {
        $pembina = auth()->user()?->pembina;

        if ($pembina) {
            $pembina->notifikasis()->where('is_read', false)->update(['is_read' => true]);
        }

        return redirect()->back();
    }

    public function destroy(Notifikasi $notifikasi)
    {
        abort_unless($notifikasi->pembina_id === auth()->user()?->pembina?->id, 403);

        $notifikasi->delete();

        return redirect()->back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    public function clearAll()
    {
        auth()->user()?->pembina?->notifikasis()->delete();

        return redirect()->back()->with('success', 'Semua notifikasi berhasil dihapus.');
    }
}