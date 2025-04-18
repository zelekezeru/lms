<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResultUpdateRequest extends FormRequest
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
            'instructor_id' => 'required|exists:instructors,id',
            'student_id' => 'required|exists:students,id',
            'weight_id' => 'required|exists:weights,id',
            'grade_id' => 'nullable|exists:grades,id',

            // If Changed
            'changed_point' => 'nullable|string|max:255',
            'changed_by' => 'nullable|exists:instructors,id',
            'changed_at' => 'nullable|date',
        ];
    }
}
