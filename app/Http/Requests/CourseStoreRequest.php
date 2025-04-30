<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // update this if you have specific authorization logic
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'credit_hours' => ['required', 'integer', 'min:1'],
            'duration' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'programs' => ['required', 'array', 'min:1'],
            'is_training' => ['boolean'],
            'status' => ['boolean'],
            'is_published' => ['boolean'],
            'is_approved' => ['boolean'],
            'is_completed' => ['boolean'],
        ];
    }
}
