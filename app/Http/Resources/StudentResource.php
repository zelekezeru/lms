<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\SectionResource;

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
            'id_no' => $this->id_no,
            'student_name' => $this->student_name,
            'father_name' => $this->father_name,
            'grand_father_name' => $this->grand_father_name,
            'mobile_phone' => $this->mobile_phone,
            'office_phone' => $this->office_phone,
            'date_of_birth' => $this->date_of_birth,
            'email' => $this->email,
            'marital_status' => $this->marital_status,
            'sex' => $this->sex,
            'address' => $this->address,
            'default_password' => $this->default_password,
            // Academic Information
            'total_credit_hours' => $this->total_credit_hours,
            'total_amount_paid' => $this->total_amount_paid,
            // Church Information
            'pastor_name' => $this->pastor_name,
            'pastor_phone' => $this->pastor_phone,
            'position_denomination' => $this->position_denomination,
            'church_name' => $this->church_name,
            'church_address' => $this->church_address,
            'office_use_notes' => $this->office_use_notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'program_id' => new ProgramResource($this->whenLoaded('program')),
            'department_id' => new DepartmentResource($this->whenLoaded('department')),
            'year_id' => new YearResource($this->whenLoaded('year')),
            'semester_id' => new SemesterResource($this->whenLoaded('semester')),
            'section_id' => new SectionResource($this->whenLoaded('section')),
            'student_image' => $this->student_image ? Storage::url($this->student_image) : null,
            'tenant_id' => new TenantResource($this->whenLoaded('tenant')),
            
            'student_status' => $this->student_status,
            'student_status_description' => $this->student_status_description,
            'student_status_id' => $this->student_status_id,
        ];
    }
}
