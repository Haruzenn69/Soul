<?php

namespace App\Http\Resources;

use App\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Kegiatan */
class KegiatanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'materi' => $this->materi,
            'jenis_kegiatan' => $this->jenis_kegiatan,
            'is_event' => $this->isEvent(),
            'deskripsi' => $this->deskripsi,
            'dokumentasi' => MediaUrl::for($this->dokumentasi),
            'tanggal_kegiatan' => $this->tanggal_kegiatan?->toDateString(),
            'tanggal_berakhir' => $this->tanggal_berakhir?->toDateString(),
            'tanggal_text' => $this->tanggalText(),
            'presensis_count' => $this->whenHas('presensis_count', fn () => (int) $this->presensis_count),
        ];
    }
}