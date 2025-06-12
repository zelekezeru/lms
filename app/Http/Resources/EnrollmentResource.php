<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
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
            'student' => new StudentResource($this->whenLoaded('student')),
            'course' => new CourseResource($this->courseOffering->course),
            'section' => new SectionResource($this->courseOffering->section),
            'instructor' => new InstructorResource($this->courseOffering->instructor ?? null),

            // if academic status is 'completed' return that and if not return the general status of the course
            'status' => $this->academic_status == 'completed' ? 'completed' : $this->status,
        ];
    }
}
