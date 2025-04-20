<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeStoreRequest extends FormRequest
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
    {dd($this->all());
        return [
            // User table validations
            'name'      => 'required|string|max:255',
            'contact_phone' => 'required|string|max:15',
            'role_name'   => 'required|exists:roles,name',
            
            // Employee table validations
            'profile_img'   => 'nullable|image:jpg,jpeg,png,gif,svg,webp|max:5150',
            'job_position'   => 'required|string|max:255',
            'employment_type'=> 'required|in:FULL_TIME,PART_TIME,CONTRACT',
            'office_hours'   => 'nullable|string|max:255',
        ];
    }
}
