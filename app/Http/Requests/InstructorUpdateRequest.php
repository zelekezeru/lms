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
            'email' => 'sometimes|email|unique:users,email,' . $this->instructor->user->id,            
            'contact_phone' => 'nullable|string|max:15',

            'specialization' => 'sometimes|string|max:255',
            'employment_type' => 'required|in:Full-time,Part-time,Contract,Visitor',
            'hire_date' => 'required|date',
            'status' => 'required|in:Active,Inactive,Suspended',
            'bio' => 'sometimes|string',
            'profile_img' => 'nullable|image:jpg,jpeg,png,gif,svg,webp|max:5150',
        ];
    }
}
