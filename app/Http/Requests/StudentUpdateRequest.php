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
            //Personal Information
            'student_name' => 'required|string|max:100',
            'father_name' => 'required|string|max:100',
            'grand_father_name' => 'sometimes|string|max:100',
            'mobile_phone' => 'sometimes|string|max:15',
            'office_phone' => 'sometimes|string|max:15',
            'date_of_birth' => 'sometimes|date',
            'email' => 'sometimes|string|max:100',
            'marital_status' => 'sometimes|string|max:10',
            'sex' => 'required|in:M,F',            
            'address' => 'required|string|max:200',

            //Academic Information
            'year_id' => 'sometimes|max:10',
            'semester' => 'sometimes|string|max:20',
            'program_id' => 'sometimes', 'exists:programs,id',
            'total_credit_hours' => 'sometimes|integer',
            'total_amount_paid' => 'sometimes|numeric',
            
            //Church Information
            'pastor_name' => 'sometimes|string|max:100',
            'pastor_phone' => 'sometimes|string|max:100',
            'position_denomination' => 'sometimes|string|max:100',
            'church_name' => 'sometimes|string|max:100',
            'church_address' => 'sometimes|string|max:200',
            'student_signature' => 'sometimes|string|max:100',
            'office_use_notes' => 'sometimes|string',
        ];
    }
}
