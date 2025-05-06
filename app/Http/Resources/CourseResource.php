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
            'programs' => $this->whenLoaded('programs'),
            'tracks' => $this->whenLoaded('tracks'),
            'isCommon' => $this->when(
                $this->pivot?->is_common !== null,
                fn () => $this->pivot->is_common
            ),
            'creditHours' => $this->credit_hours,
            'duration' => $this->duration,
            'description' => $this->description,
            'isTraining' => $this->is_training,
            'status' => $this->status,
            'isDeleted' => $this->is_deleted,
            'isPublished' => $this->is_published,
            'isApproved' => $this->is_approved,
            'isCompleted' => $this->is_completed,
        ];
    }
}
