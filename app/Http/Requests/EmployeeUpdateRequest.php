<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeUpdateRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $this->employee->user->id, // Exclude current employee's email from uniqueness check
            'contact_phone' => 'required|nullable|string|max:15',
            
            'role_name' => 'sometimes|required|exists:roles,name', // Update with actual role names from your roles table
            
            'job_position' => 'sometimes|nullable|max:255',
            'employment_type' => 'sometimes|required|in:FULL_TIME,PART_TIME,CONTRACT',
            'office_hours' => 'sometimes|nullable|string|max:255',
            'profile_img' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,gif,svg,webp|max:5150', // Optional, depending on whether the employee uploads a new image
        ];
    }
}
