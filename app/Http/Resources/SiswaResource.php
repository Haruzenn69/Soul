<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Siswa */
class SiswaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'nis' => $this->nis,
            'jenis_kelamin' => $this->jenis_kelamin,
            'jabatan' => $this->jabatan,
            'is_ketua' => $this->isKetua(),
            'is_profile_complete' => $this->isProfileComplete(),
            'kelas' => $this->whenLoaded('kelas', fn () => $this->kelas ? [
                'id' => $this->kelas->id,
                'nama' => $this->kelas->nama,
                'tingkat' => $this->kelas->tingkat,
                'jurusan' => $this->kelas->jurusan,
                'jurusan_label' => $this->kelas->jurusan_label,
                'rombel' => $this->kelas->rombel,
            ] : null),
        ];
    }
}