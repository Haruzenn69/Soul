<?php

namespace App\Http\Resources;

use App\Models\Ekskul;
use App\Models\Testimoni;
use App\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Ekskul */
class EkskulDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $pendaftarans = $this->whenLoaded('pendaftarans', collect($this->pendaftarans), collect());

        return [
            'ekskul' => (new EkskulResource($this->resource))->resolve(),
            'galeris' => EkskulGaleriResource::collection($this->whenLoaded('galeris', $this->galeris, collect()))->resolve(),
            'prestasis' => PrestasiResource::collection($this->whenLoaded('prestasis', $this->prestasis, collect()))->resolve(),
            'testimonis' => TestimoniResource::collection($this->whenLoaded('testimoniss', $this->testimoniss, collect()))->resolve(),
            'faqs' => FaqResource::collection($this->whenLoaded('faqs', $this->faqs, collect()))->resolve(),
            'kegiatans' => KegiatanResource::collection($this->whenLoaded('kegiatans', $this->kegiatans, collect()))->resolve(),
            'galeri_fotos' => collect($this->whenLoaded('galeris', $this->galeris, collect()))->pluck('foto')->map(fn ($p) => MediaUrl::for($p))->values(),
            'has_submitted_testimoni' => $request->user()
                ? $this->testimoniss()
                    ->where('user_id', $request->user()->id)
                    ->whereIn('status', [Testimoni::STATUS_PENDING, Testimoni::STATUS_APPROVED])
                    ->exists()
                : false,
        ];
    }
}
