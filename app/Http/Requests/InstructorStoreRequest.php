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
            'name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:15',

            // Instructor table validations
            'specialization' => 'required|string|max:255',
            'employment_type' => 'required|in:FULL-TIME, PART-TIME, CONTRACT,Visitor',

            'hire_date' => 'required|date',
            'status' => 'required|in:Active,Inactive,Suspended',
            'bio' => 'nullable|string',
            'courses' => 'required|min:1|array',
        ];
    }
}
