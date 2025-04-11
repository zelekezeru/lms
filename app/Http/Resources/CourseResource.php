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
            'is_deleted' => $this->is_deleted,
            'is_published' => $this->is_published,
            'is_approved' => $this->is_approved,
            'is_completed' => $this->is_completed,
        ];
    }
}
