<?php

namespace App\Http\Resources\Services;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class YoutubeOacResource extends JsonResource
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
            'channel_youtube' => $this->channel_youtube,
        ];
    }
}
