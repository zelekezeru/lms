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
            'mobile_phone' => $this->mobile_phone,
            'office_phone' => $this->office_phone,
            'date_of_birth' => $this->date_of_birth,
            'email' => $this->email,
            'marital_status' => $this->marital_status,
            'sex' => $this->sex,
            'address_1' => $this->address_1,
            'default_password' => $this->default_password,
            // Academic Information
            'year_id' => $this->year_id,
            'semester' => $this->semester,
            'program_id' => $this->program_id,
            'total_credit_hours' => $this->total_credit_hours,
            'total_amount_paid' => $this->total_amount_paid,
            // Church Information
            'pastor_name' => $this->pastor_name,
            'pastor_phone' => $this->pastor_phone,
            'position_denomination' => $this->position_denomination,
            'church_name' => $this->church_name,
            'church_address' => $this->church_address,
            'student_signature' => $this->student_signature,
            'office_use_notes' => $this->office_use_notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
