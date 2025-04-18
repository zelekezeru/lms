<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WeightStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'point' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'semester_id' => 'required|exists:semesters,id',
            'section_id' => 'nullable|exists:sections,id',
            'instructor_id' => 'nullable|exists:instructors,id',
        ];
    }
}
