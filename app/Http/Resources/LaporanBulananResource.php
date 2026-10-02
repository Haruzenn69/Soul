<?php

namespace App\Http\Resources;

use App\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\LaporanBulanan */
class LaporanBulananResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'bulan' => $this->bulan,
            'materi_kegiatan' => $this->materi_kegiatan,
            'tujuan' => $this->tujuan,
            'kehadiran' => $this->kehadiran,
            'evaluasi_keberhasilan' => $this->evaluasi_keberhasilan,
            'evaluasi_kendala' => $this->evaluasi_kendala,
            'evaluasi_solusi' => $this->evaluasi_solusi,
            'ringkasan' => $this->ringkasan,
            'status' => $this->status,
            'catatan_pembina' => $this->catatan_pembina,
            'dokumentasi' => MediaUrl::for($this->dokumentasi),
            'dokumentasi_kegiatan' => collect($this->dokumentasi_kegiatan ?? [])
                ->map(fn ($path) => MediaUrl::for($path))
                ->values(),
            'file_laporan' => MediaUrl::for($this->file_laporan),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}