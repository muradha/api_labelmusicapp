<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
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
            'email' => 'required|email|unique:users,email|max:254',
            'password' => ['required', Password::defaults(), 'confirmed', 'max:254'],
            'admin_approval' => 'nullable|string|in:APPROVED,REJECTED',
            'verify_email' => 'nullable|string|in:PENDING,APPROVED',
        ];
    }
}
