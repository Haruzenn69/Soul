<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;

class NotifikasiController extends Controller
{
    public function index()
    {
        $siswa = auth()->user()->siswa;

        // Notifikasi khusus ketua: pendaftaran masuk, pengajuan keluar, dll
        // Ketua menerima notifikasi yang relevan dengan ekskul yang dipimpinnya
        $ekskul = $siswa?->pendaftarans()->where('status', 'diterima')->first()?->ekskul;

        $notifikasis = collect();
        if ($ekskul) {
            // Ambil notifikasi yang terkait dengan ekskul ini
            $notifikasis = Notifikasi::whereHas('pendaftaran', function ($q) use ($ekskul) {
                $q->where('ekskul_id', $ekskul->id);
            })->with('pendaftaran.ekskul')->latest()->get();

            // Tambahkan notifikasi pengajuan keluar
            $notifikasis = $notifikasis->merge(
                Notifikasi::whereHas('pengajuanKeluar', function ($q) use ($ekskul) {
                    $q->where('ekskul_id', $ekskul->id);
                })->with('pengajuanKeluar.ekskul')->latest()->get()
            );

            // Tambahkan notifikasi laporan bulanan (untuk ketua yang submit)
            $notifikasis = $notifikasis->merge(
                Notifikasi::whereHas('laporanBulanan', function ($q) use ($ekskul) {
                    $q->where('ekskul_id', $ekskul->id);
                })->with('laporanBulanan.ekskul')->latest()->get()
            );

            // Urutkan by created_at desc
            $notifikasis = $notifikasis->sortByDesc('created_at')->values();
        }

        return view('ketua.notifikasi', ['notifikasis' => $notifikasis]);
    }

    public function read(Notifikasi $notifikasi)
    {
        // Otorisasi: hanya ketua ekskul yang berhak
        $siswa = auth()->user()->siswa;
        $ekskul = $siswa?->pendaftarans()->where('status', 'diterima')->first()?->ekskul;

        abort_unless($ekskul && $this->notifikasiBelongsToEkskul($notifikasi, $ekskul->id), 403);

        if (!$notifikasi->is_read) {
            $notifikasi->update(['is_read' => true]);
        }

        return redirect()->back();
    }

    public function readAll()
    {
        $siswa = auth()->user()->siswa;
        $ekskul = $siswa?->pendaftarans()->where('status', 'diterima')->first()?->ekskul;

        if ($ekskul) {
            // Tandai semua notifikasi terkait ekskul ini sebagai dibaca
            Notifikasi::whereHas('pendaftaran', function ($q) use ($ekskul) {
                $q->where('ekskul_id', $ekskul->id);
            })->where('is_read', false)->update(['is_read' => true]);

            Notifikasi::whereHas('pengajuanKeluar', function ($q) use ($ekskul) {
                $q->where('ekskul_id', $ekskul->id);
            })->where('is_read', false)->update(['is_read' => true]);
        }

        return redirect()->back();
    }

    private function notifikasiBelongsToEkskul(Notifikasi $notifikasi, int $ekskulId): bool
    {
        return ($notifikasi->pendaftaran && $notifikasi->pendaftaran->ekskul_id === $ekskulId)
            || ($notifikasi->pengajuanKeluar && $notifikasi->pengajuanKeluar->ekskul_id === $ekskulId)
            || ($notifikasi->laporanBulanan && $notifikasi->laporanBulanan->ekskul_id === $ekskulId);
    }
}