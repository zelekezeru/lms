<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstructorUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'department_id' => 'required|exists:departments,id',
            'specialization' => 'sometimes|string|max:255',
            'employment_type' => 'required|in:full-time,part-time,adjunct',
            'hire_date' => 'required|date',
            'status' => 'required|in:active,inactive,suspended',
            'bio' => 'sometimes|string',
            'profile_image' => 'sometimes|image|max:2048',
        ];
    }
}
