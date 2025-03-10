<?php

namespace App\Http\Requests\API\Legal;

use Illuminate\Foundation\Http\FormRequest;

class StoreLegalRequest extends FormRequest
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
            'message' => 'required|string|max:254',
            'conflict_type' => 'required|string|in:URGENT,COPYRIGHT,LEGAL PENALTY',
            'user_id' => 'required|numeric|exists:users,id',
        ];
    }
}
