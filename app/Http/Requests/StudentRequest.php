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
            'email' => 'nullable',
            'marital_status' => 'nullable|string|max:10',
            'sex' => 'required|in:M,F',
            'academic_year' => 'required|string|max:10',
            'semester' => 'nullable|string|max:20',
            'student_id' => 'required|string|max:20|unique:students,student_id,' . $this->student?->id,
            'program' => 'nullable|string|max:50',
            'year_of_study' => 'nullable|string|max:10',
            'pastor_name' => 'nullable|string|max:100',
            'address_1' => 'required|string|max:200',
            'position_denomination' => 'nullable|string|max:100',
            'total_credit_hours' => 'nullable|integer',
            'total_amount_paid' => 'nullable|numeric',
            'student_signature' => 'nullable|string|max:100',
            'office_use_notes' => 'nullable|string',
        ];
    }
}
