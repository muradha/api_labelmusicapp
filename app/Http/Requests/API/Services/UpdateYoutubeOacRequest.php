<?php

namespace App\Http\Requests\API\Services;

use Illuminate\Foundation\Http\FormRequest;

class UpdateYoutubeOacRequest extends FormRequest
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
            'email' => 'required|email|string|max:254',
            'artist' => 'required|string|max:255',
            'label_name' => 'required|string|max:254',
            'has_content' => 'required|numeric|min:0|max:100',
            'channel_youtube' => 'required|url|max:255',
        ];
    }
}
