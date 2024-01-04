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
            'language_type' => 'nullable|string|max:200',
            'language_title' => 'nullable|string|max:200',
            'release_type' => 'required|string|in:SINGLE,ALBUM',
            'release_date' => 'required|date',
            'release_date_original' => 'nullable|date',
            'explicit_content' => 'required|boolean',
            'cover' => 'nullable|image|max:2048',
            'country' => 'nullable|string|max:100',
            'copyright' => 'nullable|string|max:250',
            'copyright_year' => 'nullable',
            'publisher' => 'nullable|string|max:250',
            'publisher_year' => 'nullable',
            'label' => 'nullable|string|max:250',
            'description' => 'nullable|string|max:2000',
            'artist_id' => 'nullable|numeric|exists:artists,id',
        ];
    }
}
