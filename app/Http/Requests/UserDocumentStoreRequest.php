<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserDocumentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5150',
            'file' => 'nullable|file|mimes:pdf,docx|max:5120',
        ];
    }
    
}
