<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramResource extends JsonResource
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
            'description' => $this->description,
            'duration' => $this->duration,
            'language' => $this->language,
            'user_id' => $this->user_id,

            'tracks' => TrackResource::collection($this->whenLoaded('tracks')),
            'courses' => CourseResource::collection($this->whenLoaded('courses')),
            'studyModes' => StudyModeResource::collection($this->studyModes),
            'director' => new UserResource($this->whenLoaded('director')),
        ];
    }
}
