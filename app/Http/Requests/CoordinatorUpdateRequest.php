<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CoordinatorUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'center_id' => 'required|exists:centers,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5000',
        ];
    }
}
