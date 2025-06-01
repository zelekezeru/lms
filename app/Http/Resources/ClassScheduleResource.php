<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'section' => new SectionResource($this->whenLoaded('section')),
            'course' => new CourseResource($this->whenLoaded('course')),
            'semester' => new SemesterResource($this->whenLoaded('semester')),
            'instructor' => new InstructorResource($this->whenLoaded('instructor')),
            'room' => new RoomResource($this->whenLoaded('room')),
            'dayOfWeek' => $this->day_of_week,
            'startTime' => $this->start_time ? Carbon::parse($this->start_time)->format('g:i A') : null,
            'endTime' => $this->end_time ? Carbon::parse($this->end_time)->format('g:i A') : null,
        ];
    }
}
