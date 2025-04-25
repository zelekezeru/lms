<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SectionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'user_id' => 'exists:users,id',
            'program_id' => 'exists:programs,id',
            'track_id' => 'exists:tracks,id',
            'year_id' => 'exists:years,id',
            'semester_id' => 'exists:semesters,id',
        ];
    }
}
