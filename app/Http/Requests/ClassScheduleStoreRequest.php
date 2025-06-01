<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClassScheduleStoreRequest extends FormRequest
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
            'section_id'   => 'required|exists:sections,id',
            'semester_id'  => 'required|exists:semesters,id',
            'course_id'    => 'required|exists:courses,id',
            'room_id'    => 'required|exists:rooms,id',
            'day_of_week' => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time'   => 'required|date|before:end_time',
            'end_time'     => 'required|date|after:start_time',
        ];
    }
}
