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
            'idNo' => $this->id_no,
            'firstName' => $this->first_name,
            'middleName' => $this->middle_name,
            'lastName' => $this->last_name,
            'mobilePhone' => $this->mobile_phone,
            'officePhone' => $this->office_phone,
            'dateOfBirth' => $this->date_of_birth,
            'maritalStatus' => $this->marital_status,

            'profileImg' => Storage::url($this->user->profile_img),
            'user' => $this->whenLoaded('user'),
            'status' => $this->whenLoaded('status'),
            'church' => $this->whenLoaded('church'),

            'courses' => $this->whenLoaded('courses'),
            'program' => $this->whenLoaded('program'),
            'track' => $this->whenLoaded('track'),
            'section' => new SectionResource($this->whenLoaded('section')),
            'year' => $this->whenLoaded('year'),
            'semester' => $this->whenLoaded('semester'),
            'payments' => $this->whenLoaded('payments'),
            'createdBy' => $this->whenLoaded('createdBy'),
            'activeCurricula' => $this->section ? CurriculumResource::collection($this->section->getActiveCurricula()) : null,
        ];
    }
}
