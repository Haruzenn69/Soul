<?php

namespace App\Http\Controllers\Api\Ketua;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\NotifikasiResource;
use App\Models\Notifikasi;
use Illuminate\Http\JsonResponse;

class NotifikasiController extends ApiController
{
    public function index(): JsonResponse
    {
        $siswa = auth()->user()->siswa;

        abort_unless($siswa, 404, 'Data siswa tidak ditemukan.');

        $notifikasis = $siswa->notifikasis()->orderByDesc('created_at')->get();

        return $this->ok([
            'unread_count' => $notifikasis->where('is_read', false)->count(),
            'notifikasis' => NotifikasiResource::collection($notifikasis)->resolve(),
        ]);
    }

    public function read(Notifikasi $notifikasi): JsonResponse
    {
        $siswa = auth()->user()->siswa;

        abort_unless($siswa && $notifikasi->siswa_id === $siswa->id, 403, 'Akses ditolak.');

        $notifikasi->update(['is_read' => true]);

        return $this->noContent();
    }

    public function readAll(): JsonResponse
    {
        $siswa = auth()->user()->siswa;

        abort_unless($siswa, 404, 'Data siswa tidak ditemukan.');

        $siswa->notifikasis()->where('is_read', false)->update(['is_read' => true]);

        return $this->noContent();
    }
}