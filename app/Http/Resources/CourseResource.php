<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'credit_hours' => $this->credit_hours,
            'duration' => $this->duration,
            'description' => $this->description,
            'is_training' => $this->is_training,
            'status' => $this->status,
            'department_id' => $this->department_id,
            'instructor_id' => $this->instructor_id,
    
            'department' => $this->whenLoaded('department'), 
            'instructor' => $this->whenLoaded('instructor'), 
        ];
    }
}
