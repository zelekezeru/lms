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
            // User table validations
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$this->instructor->user->id,
            'contact_phone' => 'required|string|max:15',

            // Instructor table validations
            'specialization' => 'required|string|max:255',
            'courses' => 'required|min:1|array',
            'employment_type' => 'required',

            'hire_date' => 'required|date',
            'status' => 'required|in:Active,Inactive,Suspended',
            'bio' => 'nullable|string',

            // profile image
            'profile_img' => 'nullable|image|mimes:jpg,jpeg,png,gif,svg,webp|max:5150',
        ];
    }
}
