<?php

namespace App\Http\Resources;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ArtistResource extends JsonResource
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
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'email' => $this->email,
            'photo' => Storage::disk('public')->url($this->photo),
            'role' => $this->whenPivotLoaded('artist_track', fn () => $this->pivot->role),
            'role' => $this->whenPivotLoaded('artist_distribution', fn () => $this->pivot->role),
        ];
    }
}
