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
            'academic_year' => 'required|string|max:10',
            'semester' => 'required|string|max:20',
            'student_id' => 'required|string|max:20|unique:students,student_id,' . $this->student?->id,
            'program' => 'required|string|max:50',
            'year_of_study' => 'required|string|max:10',
            'student_name' => 'required|string|max:100',
            'father_name' => 'required|string|max:100',
            'grand_father_name' => 'required|string|max:100',
            'home_phone' => 'nullable|string|max:15',
            'mobile_phone' => 'nullable|string|max:15',
            'office_phone' => 'nullable|string|max:15',
            'date_of_birth' => 'required|date',
            'marital_status' => 'required|string|max:10',
            'sex' => 'required|in:M,F',
            'pastor_name' => 'nullable|string|max:100',
            'address_1' => 'required|string|max:200',
            'address_2' => 'nullable|string|max:200',
            'position_denomination' => 'required|string|max:100',
            'total_credit_hours' => 'required|integer',
            'total_amount_paid' => 'required|numeric',
            'student_signature' => 'nullable|string|max:100',
            'office_use_notes' => 'nullable|string',
        ];
    }
}
