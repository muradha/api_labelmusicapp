<?php

namespace App\Http\Requests\API\Tracks;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTrackRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:250', Rule::unique('tracks', 'title')->ignore($this->track)],
            'release_file' => 'nullable|file|mimes:wav,mp3|max:2048',
            'version' => 'required|string|max:200',
            'vocal' => 'nullable|in:YES,NO',
            'preview' => 'nullable|numeric',
            'lyric_language' => 'nullable|string|max:200',
            'size' => 'nullable|numeric',
            'music_stores' => 'nullable|array',
            'music_stores.*' => 'numeric|exists:music_stores,id',
            'authors' => 'required|array',
            'authors.*.name' => 'string|max:250',
            'featurings' => 'required|array',
            'featurings.*.name' => 'string|max:250',
            'producers' => 'required|array',
            'producers.*.name' => 'string|max:250',
            'composers' => 'required|array',
            'composers.*.name' => 'string|max:250',
            'contributors' => 'required|array',
            'contributors.*.name' => 'string|max:250',
        ];
    }
}
