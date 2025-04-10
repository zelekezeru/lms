<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreYearRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:years,name',
            'status' => 'required|string|in:active,inactive',
            'is_approved' => 'boolean',
            'is_completed' => 'boolean',
        ];
    }
}
