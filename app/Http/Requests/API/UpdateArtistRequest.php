<?php

namespace App\Http\Requests\API;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateArtistRequest extends FormRequest
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
            'first_name' => 'required|string|max:250',
            'last_name' => 'required|string|max:250',
            'email' => [
                'required', 'email', 'max:250',
                Rule::unique('artists', 'email')->ignore($this->artist)
            ],
            'photo' => 'nullable|image|max:2048',
            'admin_approval' => 'nullable|string|in:APPROVED,REJECTED',
        ];
    }
}
