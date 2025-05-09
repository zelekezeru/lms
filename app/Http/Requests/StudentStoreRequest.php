<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Personal Information
            'first_name' => 'required|string|max:100',
            'middle_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'mobile_phone' => 'nullable|string|max:15',
            'office_phone' => 'nullable|string|max:15',
            'date_of_birth' => 'nullable|date',
            'marital_status' => 'nullable|string|max:10',
            'sex' => 'required|in:M,F',
            'address' => 'nullable|string|max:200',

            // Church Information
            'pastor_name' => 'nullable|string|max:100',
            'pastor_phone' => 'nullable|string|max:100',
            'church_name' => 'nullable|string|max:100',
            'church_address' => 'nullable|string|max:200',
            'position_denomination' => 'nullable|string|max:100',

            // Foreign Keys
            'tenant_id' => 'nullable|exists:tenants,id',
            'program_id' => 'required|exists:programs,id',
            'track_id' => 'required|exists:tracks,id',
            'study_mode_id' => 'required|exists:study_modes,id',
            'year_id' => 'required|exists:years,id',
            'semester_id' => 'required|exists:semesters,id',
        ];
    }
}
