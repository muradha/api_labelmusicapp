<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TrackResource extends JsonResource
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
            'title' => $this->title,
            'file' => $this->file,
            'isrc' => $this->ISRC,
            'file_url' => Storage::disk('public')->url($this->file),
            'version' => $this->version,
            'vocal' => $this->vocal,
            'preview' => $this->preview,
            'lyric_language' => $this->lyric_language,
            'size' => $this->size,
            'authors' => $this->authors,
            'producers' => $this->producers,
            'contributors' => $this->contributors,
            'composers' => $this->composers,
            'featurings' => $this->featurings,
            'music_stores' => $this->whenLoaded('musicStores', fn () => $this->musicStores->pluck('id')->toArray()),
        ];
    }
}
