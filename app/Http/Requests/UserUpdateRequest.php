<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Personal Information
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                Rule::unique('users')->ignore(request()->user()->id),
            ],
            'roles' => ['required', 'array'],
            'roles*' => ['exists:roles,id'],
            'profile_img' => 'nullable|image|mimes:jpg,jpeg,png,gif,svg,webp|max:5150',
        ];
    }
}
