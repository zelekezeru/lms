<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstructorStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // User table validations
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'sometimes|required|string|min:8|confirmed',
            'role_name'   => 'required|exists:roles,name',
            
            // Instructor table validations
            'department_id' => 'required|exists:departments,id',
            'specialization' => 'nullable|string|max:255',
            'employment_type' => 'required|in:full-time,part-time,adjunct',
            'hire_date' => 'required|date',
            'status' => 'required|in:active,inactive,suspended',
            'bio' => 'nullable|string',
            'profile_img' => 'nullable|image|max:2048',
        ];
    }
}
