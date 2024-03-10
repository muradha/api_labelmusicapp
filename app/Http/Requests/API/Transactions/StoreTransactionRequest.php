<?php

namespace App\Http\Requests\API\Transactions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
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
            'period' => 'required|date',
            'income' => 'required|numeric|min:0|max_digits:10',
            'pay' => [Rule::when($this->income < 1, 'required|min:1|numeric|max_digits:10', 'nullable|min:0|numeric|max_digits:10')],
            'account_id' => 'required|numeric|exists:accounts,id',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'account_id.exists' => 'Account must be created first',
        ];
    }
}
