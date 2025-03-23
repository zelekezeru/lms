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
            'language' => $this->language,
            'user_id' => $this->user_id,
            
            'studyModes' => StudyModeResource::collection($this->whenLoaded('studyModes')),
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
