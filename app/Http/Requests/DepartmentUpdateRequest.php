<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255|unique:departments,name,' . $this->department->id,
            'code' => 'sometimes|string|max:50|unique:departments,code,' . $this->department->id,
            'description' => 'nullable|string',
            'established_year' => 'nullable|digits:4|integer|min:1900|max:' . date('Y'),
            'contact_email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
        ];
    }
}
