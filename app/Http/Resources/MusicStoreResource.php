<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MusicStoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'music_store_id' => $this->whenPivotLoaded('analytic_store', fn() => $this->pivot->music_store_id),
            'revenue' => $this->whenPivotLoaded('analytic_store', fn() => $this->pivot->revenue),
            'streaming' => $this->whenPivotLoaded('analytic_store', fn() => $this->pivot->streaming),
            'download' => $this->whenPivotLoaded('analytic_store', fn() => $this->pivot->download),
        ];
    }
}
