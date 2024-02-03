<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            'email' => $this->email,
            'label_name' => $this->label_name,
            'has_content' => $this->has_content,
            'release_type' => $this->whenNotNull($this->serviceable->release_type),
            'channel_youtube' => $this->whenNotNull($this->serviceable->channel_youtube),
            'youtube_url' => $this->whenNotNull($this->serviceable->youtube_url),
            'spotify_url' => $this->whenNotNull($this->serviceable->spotify_url),
        ];
    }
}
