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
            'course' => new CourseResource($this->whenLoaded('courseOffering', function() {
                return $this->courseOffering->course;
            })),
            'section' => new SectionResource($this->whenLoaded('courseOffering', function() {
                return $this->courseOffering->section;
            })),
            'instructor' => new InstructorResource($this->whenLoaded('courseOffering', function() {
                return $this->courseOffering->instructor;
            })),
            'status' => $this->status,
        ];
    }
}
