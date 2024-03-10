<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class StoreDistributionRequest extends FormRequest
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
            'title' => 'required|string|max:200|unique:distributions,title',
            'version' => 'nullable|string|min:5|max:200',
            'artist_name' => 'required|string|max:250',
            'genre' => 'required|string|max:250',
            'language_type' => 'nullable|string|max:200',
            'lyric_language' => 'required|string|max:200',
            // 'release_type' => 'required|string|in:SINGLE,ALBUM',
            'release_date' => 'required|date',
            'upc' => 'nullable|numeric|unique:distributions,upc',
            'release_date_original' => 'nullable|date',
            'cover' => 'required|image|dimensions:width=3000,height=3000|max:2048',
            'country' => 'nullable|string|max:100',
            'copyright' => 'nullable|string|max:250',
            'copyright_year' => 'nullable|numeric|between:1900,2100',
            'publisher' => 'nullable|string|max:250',
            'publisher_year' => 'nullable|numeric|between:1900,2100',
            'label' => 'nullable|string|max:250',
            'description' => 'nullable|string|max:2000',
            'artists' => 'required|array',
            'artists.*.id' => 'required|numeric|exists:artists,id',
            'artists.*.role' => 'required|string|max:50',
            'tracks' => 'required|array',
            'tracks.*.title' => 'required|string|max:250',
            'tracks.*.version' => 'required|string|max:200',
            'tracks.*.artists' => 'required|array',
            'tracks.*.artists.*.artist_id' => 'required|numeric|exists:artists,id',
            'tracks.*.artists.*.role' => 'required|string|max:50',
            'tracks.*.contributors' => 'required|array',
            'tracks.*.contributors.*.contributor_id' => 'required|numeric|exists:contributors,id',
            'tracks.*.contributors.*.role' => 'required|string|max:50',
            'tracks.*.release_file' => 'required|file|mimes:wav,mp3|max:2048',
            'tracks.*.explicit_content' => 'required|string|max:200',
            'tracks.*.genre' => 'required|string|max:100',
            'tracks.*.lyric_language' => 'required|string|max:200',
            'tracks.*.lyrics' => 'nullable|string|max:2000',
            'tracks.*.isrc' => 'nullable|string|max:255',
            'platforms' => 'required|array',
            'platforms.*.label' => 'sometimes|required|string|max:100',
            'platforms.*.value' => 'sometimes|required|string|max:100',
            'territories' => 'required|array',
            'territories.*.label' => 'sometimes|required|string|max:200',
            'territories.*.value' => 'sometimes|required|string|max:200',
        ];
    }

    public function messages(): array
    {
        return [
            'artists.*.artist_id.required' => 'Artist #:position must be selected first',
            'tracks.*.contributors.*.contributor_id.required' => 'Contributor #:second-position must be selected first',
            'tracks.*.contributors.*.contributor_id.exists' => 'Contributor #:second-position must be selected first',
            'tracks.*.artists.required' => 'Track Artist is required',
            'tracks.*.artists.*.artist_id' => 'Track Artist #:second-position must be selected first',
        ];
    }
}
