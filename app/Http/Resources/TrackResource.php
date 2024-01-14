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
            'isrc' => $this->isrc,
            'file_url' => Storage::disk('public')->url($this->file),
            'version' => $this->version,
            'vocal' => $this->vocal,
            'preview' => $this->preview,
            'lyric_language' => $this->lyric_language,
            'size' => $this->size,
        ];
    }
}
