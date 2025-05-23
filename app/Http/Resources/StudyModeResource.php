<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudyModeResource extends JsonResource
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
            'program' => new ProgramResource($this->whenLoaded('program')),
            'sections' => SectionResource::collection($this->whenLoaded('sections')),            
            'duration' => $this->whenPivotLoaded('program_study_mode', function () { 
                return $this->pivot->duration;
            }),
        ];
    }
}
