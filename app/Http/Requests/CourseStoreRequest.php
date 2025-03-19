<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {dd(request()->all());
        return [
            'name' => 'required|string|max:255|unique:courses,name',
            'credit_hours' => 'required|integer|min:1',  // Assuming credit hours must be a positive integer
            'duration' => 'nullable|integer|min:1',  // Assuming duration is an integer greater than or equal to 1
            'description' => 'nullable|string',
            'is_training' => 'nullable|boolean',  // Assuming it is either 0 or 1 (boolean)
            'status' => 'nullable|boolean',  // Assuming status is a boolean value (active/inactive)
            
            'department_id' => 'required|exists:departments,id',  // Must exist in the departments table
            'instructor_id' => 'nullable|exists:users,id',  // Optional, must exist in the users table
        ];
    }

}
