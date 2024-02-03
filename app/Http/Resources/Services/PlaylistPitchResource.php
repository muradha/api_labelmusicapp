<?php

namespace App\Http\Resources\Services;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaylistPitchResource extends JsonResource
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
            'email' => $this->whenNotNull($this->service->email ?? null),
            'artist' => $this->whenNotNull($this->service->artist ?? null),
            'label_name' => $this->whenNotNull($this->service->label_name ?? null),
            'has_content' => $this->whenNotNull($this->service->has_content ?? null),
            'release_type' => $this->release_type,
            'channel_youtube' => $this->channel_youtube,
            'youtube_url' => $this->youtube_url,
            'spotify_url' => $this->spotify_url,
        ];
    }
}
