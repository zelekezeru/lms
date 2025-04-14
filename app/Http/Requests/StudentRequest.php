<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Personal Information
            'student_name' => 'required|string|max:100',
            'father_name' => 'required|string|max:100',
            'grand_father_name' => 'nullable|string|max:100',
            'mobile_phone' => 'nullable|string|max:15',
            'office_phone' => 'nullable|string|max:15',
            'date_of_birth' => 'nullable|date',
            'email' => 'nullable|string|max:100',
            'marital_status' => 'nullable|string|max:10',
            'sex' => 'required|in:M,F',
            'address' => 'required|string|max:200',

            // Academic Information
            'year_id' => 'nullable|exists:years,id',
            'semester_id' => 'nullable|exists:semesters,id',
            'program_id' => 'nullable|exists:programs,id',
            'department_id' => 'nullable|exists:departments,id',
            'total_credit_hours' => 'nullable|integer',
            'total_amount_paid' => 'nullable|numeric',
            'total_amount_due' => 'nullable|numeric',

            // Church Information
            'pastor_name' => 'nullable|string|max:100',
            'pastor_phone' => 'nullable|string|max:100',
            'position_denomination' => 'nullable|string|max:100',
            'church_name' => 'nullable|string|max:100',
            'church_address' => 'nullable|string|max:200',

            // Additional Information
            'student_signature' => 'nullable|string|max:100',
            'default_password' => 'nullable|string|max:100',
            'id_no' => 'nullable|string|max:100',

            // ID Card Details
            'user_id' => 'nullable|exists:users,id',
            'tenant_id' => 'nullable|exists:tenants,id',
        ];
    }
}
