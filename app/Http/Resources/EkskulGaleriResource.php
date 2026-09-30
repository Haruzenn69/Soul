<?php

namespace App\Http\Resources;

use App\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\EkskulGaleri */
class EkskulGaleriResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'foto' => MediaUrl::for($this->foto),
            'caption' => $this->caption,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}