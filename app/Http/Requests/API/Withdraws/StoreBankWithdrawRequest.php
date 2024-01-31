<?php

namespace App\Http\Requests\API\Withdraws;

use Illuminate\Foundation\Http\FormRequest;

class StoreBankWithdrawRequest extends FormRequest
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
            'name' => 'required|string|max:100',
            'amount' => 'required|numeric|max_digits:10',
            'address' => 'required|string|max:254',
            'province' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|numeric|max_digits:10',
            'account_number' => 'required|string|max:100',
            'swift_code' => 'nullable|string|max:100',
            'bank_type' => 'required|string|max:100',
            'ach_code' => 'nullable|string|max:100',
            'ifsc_code' => 'nullable|string|max:100',
            'currency' => 'required|string|max:100',
        ];
    }
}
