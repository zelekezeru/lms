<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'id_no' => $this->id_no,
            'program_id' => $this->program_id,
            'department_id' => $this->department_id,
            'section_id' => $this->section_id,
            'semester_id' => $this->semester_id,
            'year_id' => $this->year_id,

            'mobile_phone' => $this->mobile_phone,
            'office_phone' => $this->office_phone,
            'date_of_birth' => $this->date_of_birth,
            'marital_status' => $this->marital_status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'tenant_id' => $this->tenant_id,
            'user_id' => $this->user_id,
            
            'profileImg'  => Storage::url($this->user->profile_img),
            'user' => $this->whenLoaded('user'),
            'status' => $this->whenLoaded('status'),
            'church' => $this->whenLoaded('church'),

            'courses' => $this->whenLoaded('courses'), 
            'program' => $this->whenLoaded('program'),
            'department' => $this->whenLoaded('department'),
            'section' => $this->whenLoaded('section'),
            'year' => $this->whenLoaded('year'),
            'semester' => $this->whenLoaded('semester'),
             
        ];
    }
}
