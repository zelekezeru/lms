<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserDocumentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'      => 'required|string|max:255',
            'description'=> 'required|string|max:500',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,gif,svg,webp|max:20480',
            'file'       => 'nullable|file|mimes:pdf,docx,doc,xlsx,xls,ppt,pptx,txt|max:20480',
        ];
    }
}
