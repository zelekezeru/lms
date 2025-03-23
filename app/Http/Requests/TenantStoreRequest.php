<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TenantStoreRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:tenants,email'],
            'phone' => ['required'],
            'address' => ['required', 'string'],
            'user_id' => ['sometimes', 'exists:users,id'],             
            'contact_person' => ['required', 'string'],
            'contact_phone' => ['required', 'string'],
            'contact_email' => ['required', 'string', 'unique:users,email'],
            'status' => ['nullable', 'in:0,1'],
            'paid' => ['nullable', 'in:0,1'],
            'logo' => ['nullable', 'file'],      
        ];
    }
}
