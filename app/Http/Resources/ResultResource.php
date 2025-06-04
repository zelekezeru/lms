<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResultResource extends JsonResource
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
            'point' => $this->point,
            'description' => $this->description,
            'changed_point' => $this->changed_point,

            'weight' => new WeightResource($this->whenLoaded('weight')),
            'student' => new StudentResource($this->whenLoaded('student')),
            'instructor' => new InstructorResource($this->whenLoaded('instructor')),
            'grade' => new GradeResource($this->whenLoaded('grade')),
            'weightId' => $this->weightId,
            'studentId' => $this->student_id,
            'instructorId' => $this->instructor_id,
            'gradeId' => $this->grade_id,
            'changed_by' => $this->changed_by,
            'changed_at' => $this->changed_at,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
