<?php

namespace App\Http\Requests\API\Transactions;

use Illuminate\Foundation\Http\FormRequest;

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
            'income' => 'required|numeric|max_digits:10',
            'pay' => 'required|numeric|max_digits:10',
            'account_id' => 'required|numeric|exists:accounts,id',
        ];
    }
}
