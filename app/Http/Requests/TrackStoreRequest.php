<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:tracks,name',
            'description' => 'nullable|string',
            'duration' => 'nullable',

            'user_id' => ['sometimes', 'exists:users,id'],
            'program_id' => ['required', 'exists:programs,id'],
        ];
    }
}
