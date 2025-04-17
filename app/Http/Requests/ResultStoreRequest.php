<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResultStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'result_point' => 'required|string|max:255',
            'result_description' => 'nullable|string|max:255',
            'changed_point' => 'nullable|string|max:255',
            'weight_id' => 'required|exists:weights,id',
            'student_id' => 'required|exists:students,id',
            'year_id' => 'required|exists:years,id',
            'semester_id' => 'required|exists:semesters,id',
            'section_id' => 'required|exists:sections,id',
            'course_id' => 'required|exists:courses,id',
            'user_id' => 'required|exists:users,id',
            'grade_id' => 'required|exists:grades,id',
            'changed_by' => 'nullable|exists:users,id',
            'changed_at' => 'nullable|date',
        ];
    }
}
