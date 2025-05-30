<?php

namespace App\Http\Resources;

use App\Models\Section;
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
            'dayOfWeek' => $this->day_of_week,
            'startDate' => $this->start_date ? Carbon::parse($this->start_date)->format('Y-m-d') : null,
            'endDate'   => $this->end_date ? Carbon::parse($this->end_date)->format('Y-m-d') : null,
            'startTime' => $this->start_time ? Carbon::parse($this->start_time)->format('g:i A') : null,
            'endTime'   => $this->end_time ? Carbon::parse($this->end_time)->format('g:i A') : null,
            'room' => $this->room,
        ];
    }
}
