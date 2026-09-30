<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Pendaftaran */
class PendaftaranResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'status_label' => match ($this->status) {
                'diterima' => 'Aktif',
                'pending' => 'Pending',
                'ditolak' => 'Ditolak',
                'nonaktif' => 'Nonaktif',
                'peringatan' => 'Peringatan',
                'keluar' => 'Keluar',
                default => $this->status,
            },
            'is_active' => $this->isActive(),
            'tanggal_daftar' => $this->tanggal_daftar?->toDateString(),
            'alasan' => $this->alasan,
            'siswa' => $this->whenLoaded('siswa', fn () => (new SiswaResource($this->siswa))->resolve()),
            'ekskul' => $this->whenLoaded('ekskul', fn () => (new EkskulResource($this->ekskul))->resolve()),
            'penilaian' => $this->whenLoaded('penilaian', fn () => $this->penilaian ? (new PenilaianResource($this->penilaian))->resolve() : null),
        ];
    }
}