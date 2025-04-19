<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            // ... other fields
            'user' => $this->user,
            'program' => $this->program,
            'department' => $this->department,
            'year' => $this->year,
            'semester' => $this->semester,
            'students' => $this->students,
            'courses' => $this->courses,
        ]; 
    }
}
