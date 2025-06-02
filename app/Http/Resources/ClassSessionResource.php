<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassSessionResource extends JsonResource
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
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
            'startTime' => $this->start_time ? Carbon::parse($this->start_time)->format('H:i:s') : null,
            'endTime'   => $this->end_time ? Carbon::parse($this->end_time)->format('H:i:s') : null,
            'status' => $this->is_completed ? "Completed!" : "Not Complete!",
            'type' => $this->type,
            'classAbout' => $this->class_about,
            'section' => new SectionResource($this->whenLoaded('section')),
            'course' => new CourseResource($this->whenLoaded('course')),
            'room' => new RoomResource($this->whenLoaded('room')),
            'attendances' => AttendanceResource::collection($this->whenLoaded('attendances')),
            'instructor' => new InstructorResource($this->whenLoaded('instructor')),
        ];
    }
}
