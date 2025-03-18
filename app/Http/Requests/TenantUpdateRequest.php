<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TenantUpdateRequest extends FormRequest
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
            'name' => 'required', 'string',
            'email' => 'required', 'email',
            'code' => 'sometimes', 'string',
            'phone' => 'sometimes',
            'address' => 'sometimes', 'string',
            'contact_person' => 'sometimes', 'string',
            'contact_phone' => 'sometimes', 'string',
            'contact_email' => 'required', 'string',
            'logo' => 'sometimes', 'file',
        ];
    }
}
