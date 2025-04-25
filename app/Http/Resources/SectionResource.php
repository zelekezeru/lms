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
            'students' => $this->whenLoaded('students'),
            'semester' => $this->whenLoaded('semester'),
            'year' => $this->whenLoaded('year'),
            'program' => $this->whenLoaded('program'),
            'track' => $this->whenLoaded('track'),
            'studyMode' => $this->whenLoaded('studyMode'),
            'courses' => $this->whenLoaded('courses'),
            'instructors' => $this->whenLoaded('instructors'),

            'status' => $this->status,
            'is_approved' => $this->is_approved,
            'is_completed' => $this->is_completed,
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
