<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);

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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'program' => new ProgramResource($this->whenLoaded('program')),
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'section' => new SectionResource($this->whenLoaded('section')),
            'semester' => new SemesterResource($this->whenLoaded('semester')),
            'year' => new YearResource($this->whenLoaded('year')),
            'user' => new UserResource($this->whenLoaded('user')),
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
            
        ];
    }
}
