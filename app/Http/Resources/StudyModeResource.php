<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
            'programs' => ProgramResource::collection($this->whenLoaded('programs')),
            'students' => StudentResource::collection($this->whenLoaded('students')),
            'sections' => SectionResource::collection($this->whenLoaded('sections')),
            'duration' => $this->whenPivotLoaded('program_study_mode', function () {
                return $this->pivot->duration;
            }),

            // only when semester is loaded with their studymodes
            'semester_start_date' => $this->whenPivotLoaded('semester_study_mode', function () {
                return Carbon::parse($this->pivot->start_date)->format('d-m-Y');
            }),
            'semester_end_date' => $this->whenPivotLoaded('semester_study_mode', function () {
                return Carbon::parse($this->pivot->end_date)->format('d-m-Y');
            }),
        ];
    }
}
