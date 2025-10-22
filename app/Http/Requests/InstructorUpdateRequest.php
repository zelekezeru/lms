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
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,'.$this->instructor->user->id,
            'contact_phone' => 'sometimes|required||string|max:15',

            'specialization' => 'sometimes|required|string|max:255',
            'courses' => 'sometimes|required|array|min:1',
            'employment_type' => 'required',
            'hire_date' => 'required|date',
            'status' => 'required|in:ACTIVE,INACTIVE,SUSPENDED',
            'bio' => 'sometimes|string',
            'profile_img' => 'nullable|image:jpg,jpeg,png,gif,svg,webp|max:5150',
        ];
    }
}
