<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'code' => $this->code,
            'programs' => $this->whenLoaded('programs'),
            'tracks' => $this->whenLoaded('tracks'),
            'instructors' => InstructorResource::collection($this->whenLoaded('instructors')),
            'sections' => SectionResource::collection($this->whenLoaded('courseSectionAssignments', function () {
                return $this->courseSectionAssignments->map(fn($assignment) => $assignment->section);
            })),
            'sectionsCount' => $this->sectionsCount ?? null,
            'instructor' => $this->whenLoaded('courseSectionAssignments', function () {
                $assignment = $this->courseSectionAssignments->first();
                return $assignment && $assignment->instructor
                    ? new InstructorResource($assignment->instructor)
                    : null;
            }),
            'isCommon' => $this->when(
                $this->pivot?->is_common !== null,
                fn() => $this->pivot->is_common
            ),
            'credit_hours' => $this->credit_hours,
            'duration' => $this->duration,
            'description' => $this->description,
            'isTraining' => $this->is_training,
            'status' => $this->status,
            'isDeleted' => $this->is_deleted,
            'isPublished' => $this->is_published,
            'isApproved' => $this->is_approved,
            'isCompleted' => $this->is_completed,
            'grades' => $this->whenLoaded('grades'),
        ];
    }
}
