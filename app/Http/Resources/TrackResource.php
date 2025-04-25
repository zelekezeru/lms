<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrackResource extends JsonResource
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
            'description' => $this->description,
            'code' => $this->code,
            'duration' => $this->duration,
            'user_id' => $this->user_id,
            'program_id' => $this->program_id,

            'user' => new UserResource($this->whenLoaded('user')),
            'program' => new ProgramResource($this->whenLoaded('program')),
            'courses' => CourseResource::collection($this->whenLoaded('courses')),
            'curriculums' => CurriculumResource::collection($this->whenLoaded('curriculums')),
            'sections' => SectionResource::collection($this->whenLoaded('Sections')),

        ];
    }
}
