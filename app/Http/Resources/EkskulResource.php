<?php

namespace App\Http\Resources;

use App\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Ekskul */
class EkskulResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $pendaftarans = $this->relationLoaded('pendaftarans')
            ? $this->pendaftarans
            : null;

        return [
            'id' => $this->id,
            'nama_ekskul' => $this->nama_ekskul,
            'tagline' => $this->tagline,
            'deskripsi' => $this->deskripsi,
            'tujuan' => $this->tujuan,
            'jadwal' => $this->jadwal,
            'logo' => MediaUrl::for($this->logo),
            'cover' => MediaUrl::for($this->cover),
            'is_open_recruitment' => (bool) $this->is_open_recruitment,
            'status' => (bool) $this->status,
            'pembina' => $this->whenLoaded('pembina', fn () => $this->pembina ? [
                'id' => $this->pembina->id,
                'nama' => $this->pembina->nama,
            ] : null),
            'pelatih' => $this->whenLoaded('pelatih', fn () => $this->pelatih ? [
                'id' => $this->pelatih->id,
                'nama' => $this->pelatih->nama,
                'no_hp' => $this->pelatih->no_hp,
            ] : null),
            'anggota_count' => $pendaftarans !== null
                ? $pendaftarans->count()
                : $this->pendaftarans()->whereIn('status', ['diterima', 'peringatan'])->count(),
        ];
    }
}