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
            'student_name' => $this->student_name,
            'father_name' => $this->father_name,
            'grand_father_name' => $this->grand_father_name,
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

            'church_name' => $this->church_name,
            'church_address' => $this->church_address,
            'position_denomination' => $this->position_denomination,
            'pastor_name' => $this->pastor_name,
            'pastor_phone' => $this->pastor_phone,

            'user_id' => $this->user_id,
            'status' => $this->status,
            'is_approved' => $this->is_approved,
            'is_completed' => $this->is_completed,
            'is_active' => $this->is_active,
            'is_deleted' => $this->is_deleted,
            'is_verified' => $this->is_verified,
            'is_enrolled' => $this->is_enrolled,
            'is_graduated' => $this->is_graduated,
            'is_scholarship' => $this->is_scholarship,

            'is_scholarship_approved' => $this->is_scholarship_approved,
            'is_scholarship_completed' => $this->is_scholarship_completed,
            'is_scholarship_active' => $this->is_scholarship_active,
            'is_scholarship_deleted' => $this->is_scholarship_deleted,
            'is_scholarship_verified' => $this->is_scholarship_verified,
            'is_scholarship_enrolled' => $this->is_scholarship_enrolled,
            
            'profileImg'  => Storage::url($this->user->profile_img),
            'user' => $this->whenLoaded('user'),
            'courses' => $this->whenLoaded('courses'), 
            'program' => $this->whenLoaded('program'),
            'department' => $this->whenLoaded('department'),
            'section' => $this->whenLoaded('section'),
            'year' => $this->whenLoaded('year'),
            'semester' => $this->whenLoaded('semester'),
             
        ];
    }
}
