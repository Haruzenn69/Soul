<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\User */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $siswa = $this->relationLoaded('siswa') ? $this->siswa : $this->siswa()->with('kelas')->first();
        $pembina = $this->relationLoaded('pembina') ? $this->pembina : $this->pembina()->first();
        $aktiv = $siswa ? $siswa->activePendaftaran()?->loadMissing('ekskul') : null;

        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'onboarding_completed_at' => $this->onboarding_completed_at,
            'needs_onboarding' => $this->needsOnboarding(),
            'needs_profile_completion' => $this->needsProfileCompletion(),
            'siswa' => $siswa ? (new SiswaResource($siswa))->resolve() : null,
            'pembina' => $pembina ? [
                'id' => $pembina->id,
                'nama' => $pembina->nama,
                'nip' => $pembina->nip,
                'jenis_kelamin' => $pembina->jenis_kelamin,
            ] : null,
            'aktiv_ekskul' => $aktiv?->ekskul ? (new EkskulResource($aktiv->ekskul))->resolve() : null,
            'jabatan' => $siswa?->jabatan,
        ];
    }
}