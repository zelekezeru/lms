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
            'id' => 'required', 'string',
            'name' => 'required', 'string',
            'email' => 'required', 'email',
            'code' => 'required', 'string',
            // 'password'  => 'sometimes|required|string|min:8|confirmed',
            'phone' => 'required',
            'address' => 'required', 'string',
            'contact_person' => 'required', 'string',
            'contact_phone' => 'required', 'string',
            'aggrement' => 'required', 'string',
            'aggrement_image' => 'nullable|image:jpeg,jpg,png',
            'status' => 'required', 'boolean',
            'allowed' => 'required', 'boolean',
            'paid' => 'required', 'boolean',
        ];
    }
}
