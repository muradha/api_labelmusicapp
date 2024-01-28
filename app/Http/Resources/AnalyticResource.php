<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnalyticResource extends JsonResource
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
            'period' => $this->period,
            'artist' => ArtistResource::make($this->whenLoaded('artist')),
            'total_revenue' => $this->whenLoaded('stores', function() {
                return $this->stores->sum('pivot.revenue');
            }),
            'total_streaming' => $this->whenLoaded('stores', function() {
                return $this->stores->sum('pivot.streaming');
            }),
            'total_download' => $this->whenLoaded('stores', function() {
                return $this->stores->sum('pivot.download');
            }),
            'shops' => MusicStoreResource::collection($this->whenLoaded('stores')),
            'artists' => ArtistResource::collection($this->whenLoaded('artists')),
        ];
    }
}
