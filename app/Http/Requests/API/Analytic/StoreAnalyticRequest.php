<?php

namespace App\Http\Requests\API\Analytic;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAnalyticRequest extends FormRequest
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
            'period' => ['required', 'date', Rule::unique('analytics', 'period')->where(fn (Builder $query) => $query->where('artist_id', request('artist_id'))->exists())],
            'user_id' => 'sometimes|numeric|exists:users,id',
            'artist_id' => 'required|numeric|exists:artists,id',
            'shops.*' => 'required|array',
            'shops.*.revenue' => 'required|numeric|max_digits:10',
            'shops.*.streaming' => 'required|numeric|max_digits:10',
            'shops.*.download' => 'required|numeric|max_digits:10',
            'shops.*.music_store_id' => 'required|numeric|exists:music_stores,id',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'period' => $this->period . '-01',
        ]);
    }
}
