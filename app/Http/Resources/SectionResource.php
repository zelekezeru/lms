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
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'students' => StudentResource::collection($this->whenLoaded('students', function () {
                return $this->students->sortBy('name')->values();
            })),
            'semester' => new SemesterResource($this->whenLoaded('semester')),
            'year' => new YearResource($this->whenLoaded('year')),
            'classSchedules' => ClassScheduleResource::collection($this->whenLoaded('classSchedules')),
            'center' => new CenterResource($this->whenLoaded('center')),
            'program' => new ProgramResource($this->whenLoaded('program')),
            'track' => new TrackResource($this->whenLoaded('track')),
            'user' => new UserResource($this->whenLoaded('user')),
            'studyMode' => new StudyModeResource($this->whenLoaded('studyMode')),
            'instructors' => $this->whenLoaded('instructors'),
            'grades' => $this->whenLoaded('grades'),

            'yearLevel' => $this->yearLevel(),
            'semesterLevel' => $this->semester->level,
            'status' => $this->status,
            'isApproved' => $this->is_approved,
            'isCompleted' => $this->is_completed,
            'courses' => $this->whenLoaded('courseOfferings', function () {
                return array_values($this->courseOfferings->map(function ($offering) {
                    return [
                        'id' => $offering->course->id,
                        'name' => $offering->course->name,
                        'code' => $offering->course->code,
                        'creditHour' => $offering->course->credit_hours,
                        'yearLevel' => intval($offering->year_level),
                        'semester' => intval($offering->semester_level),
                        'instructor' => $offering->instructor ? [
                            'id' => $offering->instructor->id,
                            'name' => $offering->instructor->user->name,
                        ] : null,
                    ];
                })->all()); // force normal array
            }),

        ];
    }
}
