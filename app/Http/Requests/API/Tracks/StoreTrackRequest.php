<?php

namespace App\Http\Requests\API\Tracks;

use Illuminate\Foundation\Http\FormRequest;

class StoreTrackRequest extends FormRequest
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
            'title' => 'required|string|max:250|unique:tracks,title',
            'file' => 'required|file|mimes:wav,mp3|max:2048',
            'version' => 'required|string|max:200',
            'vocal' => 'nullable|in:YES,NO',
            'preview' => 'nullable|numeric',
            'lyric_language' => 'nullable|string|max:200',
            'size' => 'nullable|numeric',
            'distribution_id' => 'required|numeric|exists:distributions,id',
        ];
    }
}
