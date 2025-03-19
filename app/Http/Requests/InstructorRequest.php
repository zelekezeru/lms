<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstructorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 'user_id' => 'required|exists:users,id',
            'department_id' => 'required|exists:departments,id',
            'specialization' => 'required|string|max:255',
            'employment_type' => 'required|in:full-time,part-time,adjunct',
            'hire_date' => 'required|date',
            'status' => 'required|in:active,inactive,suspended',
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|image|max:2048',
        ];
    }
}
