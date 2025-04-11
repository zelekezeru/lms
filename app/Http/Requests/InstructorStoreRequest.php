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
            'password'  => 'required|string|min:8|confirmed',
            
            // Instructor table validations
            'specialization' => 'nullable|string|max:255',
            'employment_type' => 'required|in:full-time,part-time,adjunct',
            'hire_date' => 'required|date',
            'status' => 'required|in:Active,Inactive,Suspended',
            'bio' => 'nullable|string',
            'profile_img' => 'nullable|image:jpg,jpeg,png,gif,svg,webp|max:5150',
        ];
    }
}
