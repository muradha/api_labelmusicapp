<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
                'required', 'email', 'max:254',
                Rule::unique('users', 'email')->ignore($this->user)
            ],
            'password' => ['nullable', Password::defaults(), 'max:254'],
            'admin_approval' => 'nullable|string|in:APPROVED,REJECTED,PENDING',
            'verify_email' => 'nullable|string|in:PENDING,APPROVE',
            'role' => 'nullable|string|in:admin,operator',
            'birth_date' => 'nullable|date',
            'phone_number' => 'nullable|string|max:10',
            'company_name' => 'nullable|string',
            'street' => 'nullable|string',
            'city' => 'nullable|string',
            'province' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'country' => 'nullable|string'
        ];
    }
}
