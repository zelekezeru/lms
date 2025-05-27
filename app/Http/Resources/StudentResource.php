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
            'address' => $this->address,
            'sex' => $this->sex,
            'mobilePhone' => $this->mobile_phone,
            'officePhone' => $this->office_phone,
            'dateOfBirth' => $this->date_of_birth,
            'maritalStatus' => $this->marital_status,
            'user_id' => $this->user_id,

            'profileImg' => Storage::url($this->user->profile_img),
            'user' => $this->whenLoaded('user'),
            'status' => $this->whenLoaded('status'),
            'church' => $this->whenLoaded('church'),

            'courses' => CourseResource::collection($this->whenLoaded('courses')),
            'program' => new ProgramResource($this->whenLoaded('program')),
            'track' => new TrackResource($this->whenLoaded('track')),
            'section' => new SectionResource($this->whenLoaded('section')),
            'studyMode' => new StudyModeResource($this->whenLoaded('studyMode')),
            'year' => new YearResource($this->whenLoaded('year')),
            'semester' => new SemesterResource($this->whenLoaded('semester')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'createdBy' => $this->whenLoaded('createdBy'),
            'activeCurricula' => $this->whenLoaded('courses', function () {
                return $this->courses->wherePivot('pivot.status', 'enrolled');
            }),
            'documents' => UserDocumentResource::collection($this->whenLoaded('documents')),

            'results' => $this->whenLoaded('results'),
            'grades' => $this->whenLoaded('grades'),
        ];
    }
}
