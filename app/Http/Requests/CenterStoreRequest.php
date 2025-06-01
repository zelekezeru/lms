<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CenterStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'user_id' => 'sometimes|exists:users,id',
            'status' => 'required|in:Active,Inactive',
            'address' => 'required|string|max:255',
        ];
    }
}
