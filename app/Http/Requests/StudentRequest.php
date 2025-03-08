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
            'student_name' => 'required|string|max:100',
            'father_name' => 'required|string|max:100',
            'grand_father_name' => 'nullable|string|max:100',
            'mobile_phone' => 'nullable|string|max:15',
            'office_phone' => 'nullable|string|max:15',
            'date_of_birth' => 'nullable|date',
            'email' => 'nullable|string|max:100',
            'marital_status' => 'nullable|string|max:10',
            'sex' => 'required|in:M,F',
            'academic_year' => 'nullable|max:10',
            'semester' => 'nullable|string|max:20',
            'program' => 'nullable|string|max:50',
            'pastor_name' => 'nullable|string|max:100',
            'pastor_phone' => 'nullable|string|max:100',
            'address_1' => 'required|string|max:200',
            'position_denomination' => 'nullable|string|max:100',
            'church_name' => 'nullable|string|max:100',
            'church_address' => 'nullable|string|max:200',
            'total_credit_hours' => 'nullable|integer',
            'total_amount_paid' => 'nullable|numeric',
            'student_signature' => 'nullable|string|max:100',
            'office_use_notes' => 'nullable|string',
        ];
    }
}
