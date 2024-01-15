<?php

namespace App\Http\Requests\API\Account;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccountRequest extends FormRequest
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
            'account_number' => 'required|string|max:250',
            'balance' => 'required|numeric|max_digits:10',
            'user_id' => 'required|numeric|exists:users,id|unique:accounts,user_id',
            'bank_id' => 'required|numeric|exists:banks,id'
        ];
    }
}
