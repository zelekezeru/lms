<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeResource extends JsonResource
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
            'grade_point' => $this->grade_point,
            'grade_letter' => $this->grade_letter,
            'grade_description' => $this->grade_description,
            'grade_scale' => $this->grade_scale,
            'grade_complaint' => $this->grade_complaint,
            'grade_comment' => $this->grade_comment,
            'changed_grade' => $this->changed_grade,
            'grade_status' => $this->grade_status,
            'changed_by' => $this->changed_by,

            'user_id' => $this->user_id,
            'course_id' => $this->course_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'section' => $this->whenLoaded('section'),
            'course' => $this->whenLoaded('course'),
            'year' => $this->whenLoaded('year'),
            'semester' => $this->whenLoaded('semester'),
            'student' => $this->whenLoaded('student'),
            'instructor' => $this->whenLoaded('instructor'),
        ];
    }
}
