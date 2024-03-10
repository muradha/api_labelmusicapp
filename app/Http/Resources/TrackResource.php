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
        $file_url = $this->file && Storage::disk('public')->exists($this->file) ? Storage::disk('public')->url($this->file) : null;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'file' => $this->file,
            'isrc' => $this->ISRC,
            'file_url' => $file_url,
            'version' => $this->version,
            'lyric_language' => $this->lyric_language,
            'explicit_content' => $this->explicit_content,
            'genre' => $this->genre,
            'contributors' => $this->whenLoaded('contributors') ? ContributorResource::collection($this->contributors) : null,
            'artists' => $this->whenLoaded('artists') ? ArtistResource::collection($this->artists) : null,
        ];
    }
}
