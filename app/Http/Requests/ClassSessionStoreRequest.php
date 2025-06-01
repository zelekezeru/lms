<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClassSessionStoreRequest extends FormRequest
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
            'section_id' => 'required|exists:sections,id',
            'course_id' => 'required|exists:courses,id',
            'instructor_id' => 'required|exists:instructors,id',
            'semester_id' => 'required|exists:semesters,id',
            'room_id' => 'required|exists:rooms,id',
            'date_time' => 'required|date',
            'class_about' => 'nullable|string|max:255',
        ];
    }
}
