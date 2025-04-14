<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudyModeUpdateRequest extends FormRequest
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
            'program_id' => 'sometimes | required | exists:programs,id',
            'mode' => 'sometimes | required | in:REGULAR,EXTENSION,DISTANCE,ONLINE',
            'duration' => 'sometimes | required | numeric',
            'fees' => 'sometimes | required | numeric'
        ];
    }
}
