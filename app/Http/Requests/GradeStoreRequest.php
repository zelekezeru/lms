<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GradeStoreRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            // Student and grade details
            'student_id' => 'required|exists:students,id',
            'grade_point' => 'required|numeric|min:0|max:100',
            'grade_letter' => 'required|string|max:10',
            'grade_description' => 'nullable|string|max:255',
            'grade_scale' => 'required|numeric|min:0|max:100',
            'grade_complaint' => 'required|boolean',
            'grade_comment' => 'nullable|string|max:255',
            'changed_grade' => 'nullable|numeric|min:0|max:100',

            // Grade status and metadata
            'grade_status' => 'required|string|in:Pending,Final',
            'changed_by' => 'nullable|exists:users,id',

            // Related entities
            'user_id' => 'required|exists:users,id',
            'year_id' => 'required|exists:years,id',
            'semester_id' => 'required|exists:semesters,id',
            'section_id' => 'required|exists:sections,id',
            'course_id' => 'required|exists:courses,id',
        ];
    }
}
