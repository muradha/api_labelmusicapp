<?php

namespace App\Http\Requests\API\Analytic;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnalyticRequest extends FormRequest
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
            'user_id' => 'sometimes|numeric|exists:users,id',
            'artist_id' => 'required|numeric|exists:artists,id',
            'shops.*' => 'required|array',
            'shops.*.revenue' => 'required|numeric|max_digits:10',
            'shops.*.streaming' => 'required|numeric|max_digits:10',
            'shops.*.download' => 'required|numeric|max_digits:10',
            'shops.*.music_store_id' => 'required|numeric|exists:music_stores,id',
        ];
    }
}
