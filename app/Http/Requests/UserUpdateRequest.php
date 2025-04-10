<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->user->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'profile_img' => 'nullable|image|mimes:jpg,jpeg,png,gif,svg,webp|max:5150',
        ];
    }
}
