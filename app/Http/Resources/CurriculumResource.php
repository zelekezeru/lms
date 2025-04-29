<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurriculumResource extends JsonResource
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
            'track' => new TrackResource($this->whenLoaded('track')),
            'studyMode' => new StudyModeResource($this->whenLoaded('studyMode')),
            'course' => new CourseResource($this->whenLoaded('course')),
            'yearLevel' => $this->year_level,
            'semester' => $this->semester,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
