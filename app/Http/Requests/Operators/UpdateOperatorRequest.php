<?php

namespace App\Http\Requests\Operators;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOperatorRequest extends FormRequest
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
            'name' => 'required|max:254|string|min:5',
            'email' => [
                'required', 'email',
                Rule::unique('users', 'email')->ignore($this->user)
            ],
            'password' => 'nullable|string|max:254',
        ];
    }
}
