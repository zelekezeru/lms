<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
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
            
            'program' => $this->whenLoaded('program'),            
            'user' => $this->whenLoaded('user'),

        ];        
    }
}
