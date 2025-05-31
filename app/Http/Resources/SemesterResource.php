<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SemesterResource extends JsonResource
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
            'level' => $this->level,
            'status' => $this->status,
            'is_approved' => $this->is_approved,
            'is_completed' => $this->is_completed,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'created_at' => $this->created_at,

            'students' => StudentResource::collection($this->whenLoaded('students')),
            'courses' => CourseResource::collection($this->whenLoaded('courses')),
            'grades' => GradeResource::collection($this->whenLoaded('grades')),
            'year' => new YearResource($this->whenLoaded('year')),

        ];
    }
}
