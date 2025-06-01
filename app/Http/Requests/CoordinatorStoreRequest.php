<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CoordinatorStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'center_id' => 'required|exists:centers,id',
        ];
    }
}
