<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            //Personal Information
            'first_name' => 'required|string|max:100',
            'middle_name' => 'required|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'mobile_phone' => 'sometimes|string|max:15',
            'office_phone' => 'sometimes|string|max:15',
            'date_of_birth' => 'sometimes|date',
            'email' => 'sometimes|string|max:100',
            'marital_status' => 'sometimes|string|max:10',
            'sex' => 'required|in:M,F',            
            'address' => 'required|string|max:200',

            //Academic Information
            'year_id'=> 'sometimes', 'exists:years,id',
            'semester_id' => 'sometimes', 'exists:semesters,id',
            'id_no' => 'sometimes|string|max:100',
            'program_id' => 'sometimes', 'exists:programs,id',
            'department_id' => 'sometimes', 'exists:departments,id',
            'section_id' => 'sometimes', 'exists:sections,id',
            'student_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'student_signature' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'total_credit_hours' => 'sometimes|integer',
            'total_amount_paid' => 'sometimes|numeric',
            
            //Church Information
            'pastor_name' => 'sometimes|string|max:100',
            'pastor_phone' => 'sometimes|string|max:100',
            'position_denomination' => 'sometimes|string|max:100',
            'church_name' => 'sometimes|string|max:100',
            'church_address' => 'sometimes|string|max:200',
            'office_use_notes' => 'sometimes|string',
        ];
    }
}
