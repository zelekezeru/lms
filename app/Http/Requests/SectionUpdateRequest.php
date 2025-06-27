<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SectionUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'program_id' => 'exists:programs,id',
            'track_id' => 'exists:tracks,id',
            'year_id' => 'exists:years,id',
            'semester_id' => 'exists:semesters,id',
            'study_mode_id' => 'exists:study_modes,id',
        ];
    }
}
