<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CurriculumStoreRequest extends FormRequest
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
            'study_mode_id' => 'required|integer|exists:study_modes,id',
            'track_id' => 'required|integer|exists:tracks,id',
            'courses' => 'required|array|min:1',
            'courses.*' => 'integer|exists:courses,id',
            'year_level' => 'required|integer',
            'semester' => 'required|integer',
            'description' => 'nullable|string|max:255',
        ];
    }
}
