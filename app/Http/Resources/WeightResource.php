<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WeightResource extends JsonResource
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
            'name' => $this->name,
            'point' => $this->point,
            'description' => $this->description,

            'instructor' => new InstructorResource($this->whenLoaded('instructor')),
            'section' => new SectionResource($this->whenLoaded('section')),
            'semester' => new SemesterResource($this->whenLoaded('semester')),
            'course' => new CourseResource($this->whenLoaded('course')),
            'results' => ResultResource::collection($this->whenLoaded('results')),

            'instructorId' => $this->instructor_id,
            'sectionId' => $this->section_id,
            'semesterId' => $this->semester_id,
            'courseId' => $this->course_id,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
