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
            'upc' => 'required|numeric|unique:distributions,upc',
            'release_date_original' => 'nullable|date',
            'explicit_content' => 'required|boolean',
            'cover' => 'required|image|dimensions:width:3000,height:3000|max:2048',
            'country' => 'nullable|string|max:100',
            'copyright' => 'nullable|string|max:250',
            'copyright_year' => 'nullable|numeric|between:1900,2100',
            'publisher' => 'nullable|string|max:250',
            'publisher_year' => 'nullable|numeric|between:1900,2100',
            'label' => 'nullable|string|max:250',
            'description' => 'nullable|string|max:2000',
            'artist_id' => 'nullable|numeric|exists:artists,id',
        ];
    }

     /**
     * Prepare inputs for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'explicit_content' => $this->toBoolean($this->explicit_content),
        ]);
    }

    /**
     * Convert to boolean
     *
     * @param $booleable
     * @return boolean
     */
    private function toBoolean($booleable)
    {
        return filter_var($booleable, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
}
