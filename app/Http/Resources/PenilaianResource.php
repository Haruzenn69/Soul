<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Penilaian */
class PenilaianResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'periode' => $this->periode,
            'total_pertemuan' => (int) $this->total_pertemuan,
            'total_hadir' => (int) $this->total_hadir,
            'total_izin' => (int) $this->total_izin,
            'total_sakit' => (int) $this->total_sakit,
            'total_alpha' => (int) $this->total_alpha,
            'persentase_kehadiran' => (float) $this->persentase_kehadiran,
            'nilai_sikap' => (float) $this->nilai_sikap,
            'nilai_keaktifan' => (float) $this->nilai_keaktifan,
            'nilai_keterampilan' => (float) $this->nilai_keterampilan,
            'nilai_akhir' => (float) $this->nilai_akhir,
            'predikat' => $this->predikat,
            'catatan' => $this->catatan,
            'status' => $this->status,
            'dikirim_at' => $this->dikirim_at?->toIso8601String(),
        ];
    }
}