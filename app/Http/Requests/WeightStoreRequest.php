<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WeightStoreRequest extends FormRequest
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
    {
        return [
            'name' => 'required|string|max:255',
            'weight_point' => 'required|numeric|min:0|max:100',
            'weight_description' => 'nullable|string|max:255',
            'user_id' => 'required|exists:users,id',
            'year_id' => 'required|exists:years,id',
            'semester_id' => 'required|exists:semesters,id',
            'course_id' => 'required|exists:courses,id',
        ];
    }
}
