<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class DistributionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $cover_url = $this->cover && Storage::disk('public')->exists($this->cover) ? Storage::disk('public')->url($this->cover) : null;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'artist_name' => $this->artist_name,
            'version' => $this->version,
            'genre' => $this->genre,
            'lyric_language' => $this->lyric_language,
            'language_title' => $this->language_title,
            'release_type' => $this->release_type,
            'release_date' => $this->release_date,
            'release_date_original' => $this->release_date_original,
            'upc' => $this->upc,
            'cover' => Storage::disk('public')->url($this->cover),
            'cover_url' => $cover_url,
            'country' => $this->country,
            'copyright' => $this->copyright,
            'copyright_year' => $this->copyright_year,
            'publisher' => $this->publisher,
            'publisher_year' => $this->publisher_year,
            'label' => $this->label,
            'submit_status' => $this->submit_status,
            'verification_status' => $this->verification_status,
            'description' => $this->description,
            'platforms' => $this->whenLoaded('store', fn () => $this->store->platforms),
            'territories' => $this->whenLoaded('store', fn () => $this->store->territories),
            'artists' => ArtistResource::collection($this->whenLoaded('artists')),
            // 'tracks' => $this->tracks,
        ];
    }
}
