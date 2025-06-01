<?php

namespace App\Http\Resources;

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
            'date' => optional($this->date_time)->format('Y-m-d'),
            'time' => optional($this->date_time)->format('H:i:s'),
            'classAbout' => $this->class_about,
            'section' => new SectionResource($this->whenLoaded('section')),
            'course' => new CourseResource($this->whenLoaded('course')),
            'room' => new RoomResource($this->whenLoaded('room')),
            'attendances' => AttendanceResource::collection($this->whenLoaded('attendances')),
            'instructor' => new InstructorResource($this->whenLoaded('instructor')),
        ];
    }
}
