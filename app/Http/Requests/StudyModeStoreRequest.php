<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudyModeStoreRequest extends FormRequest
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
            'program_id' => 'required | exists:programs,id',
            'mode' => 'required | in:REGULAR,EXTENSION,DISTANCE,ONLINE',
            'duration' => 'required | numeric',
            'fees' => 'required | numeric'
        ];
    }
}
