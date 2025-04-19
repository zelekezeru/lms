<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'user' => new UserResource($this->whenLoaded('user')),
            'program' => new ProgramResource($this->whenLoaded('program')),
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'year' => new YearResource($this->whenLoaded('year')),
            'semester' => new SemesterResource($this->whenLoaded('semester')),
            'students' => StudentResource::collection($this->whenLoaded('students')),
            'courses' => $this->whenLoaded('courseSectionAssignments', function () {
                return $this->courseSectionAssignments->map(function ($assignment) {
                    return [
                        'id' => $assignment->course->id,
                        'name' => $assignment->course->name,
                        'code' => $assignment->course->code,
                        'creditHour' => $assignment->course->credit_hours,
                        'instructor' => $assignment->instructor ? [
                            'id' => $assignment->instructor->id,
                            'name' => $assignment->instructor->user->name,
                        ] : null,
                    ];
                });
            }),
        ];
    }
}
