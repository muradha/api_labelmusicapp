<?php

namespace App\Http\Requests\API\Tracks;

use Illuminate\Foundation\Http\FormRequest;

class StoreTrackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:250',
            'release_file' => 'required|file|mimes:wav,mp3|max:2048',
            'version' => 'nullable|string|max:200',
            'explicit_content' => 'required|string|max:200',
            'genre' => 'required|string|max:100',
            'lyric_language' => 'required|string|max:200',
            'lyrics' => 'nullable|string|max:2000',
            'isrc' => 'nullable|string|max:255',
            'artists' => 'required|array',
            'artists.*.id' => 'required|numeric|exists:artists,id',
            'artists.*.role' => 'required|string|max:50',
            'contributors' => 'required|array',
            'contributors.*.id' => 'required|numeric|exists:contributors,id',
            'contributors.*.role' => 'required|string|max:50',
            'distribution_id' => 'required|numeric|exists:distributions,id'
        ];
    }
}
