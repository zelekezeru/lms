<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'semester_id' => 'required|exists:semesters,id',
            'room_id' => 'nullable|exists:rooms,id',
            'start_date_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_date_time'],
            'type' => ['required', Rule::in(['in-person', 'online'])],
            'class_about' => ['nullable', 'string', 'max:255'],
        ];
    }
}
