<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateYearRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:years,name,' . $this->year->id,
            'status' => 'required|string|in:active,inactive',
            'is_approved' => 'boolean',
            'is_completed' => 'boolean',
        ];
    }
}
