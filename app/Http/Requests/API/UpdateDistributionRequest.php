<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDistributionRequest extends FormRequest
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
            'title' => 'required|string|max:200',
            'version' => 'nullable|string|min:5|max:200',
            'artist_name' => 'required|string|max:250',
            'genre' => 'required|string|max:250',
            'language_type' => 'nullable|string|max:200',
            'lyric_language' => 'required|string|max:200',
            // 'release_type' => 'required|string|in:SINGLE,ALBUM',
            'release_date' => 'required|date',
            'upc' => 'nullable|numeric|max_digits:14',
            'release_date_original' => 'nullable|date',
            'cover' => 'nullable|image|dimensions:width=3000,height=3000|max:2048',
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
            'platforms' => 'required|array',
            'platforms.*.label' => 'sometimes|required|string|max:100',
            'platforms.*.value' => 'sometimes|required|string|max:100',
            'territories' => 'required|array',
            'territories.*.label' => 'sometimes|required|string|max:200',
            'territories.*.value' => 'sometimes|required|string|max:200',
        ];
    }

    public function messages(): array {
        return [
            'artists.*.id.required' => 'Artist #:position must be selected first',
        ];
    }
}
