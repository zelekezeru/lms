<?php

namespace App\Http\Resources;

use App\Models\Section;
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
            'dayOfWeek' => $this->day_of_week,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
            'startTime' => $this->start_time,
            'endTime' => $this->end_time,
            'room' => $this->room,
        ];
    }
}
