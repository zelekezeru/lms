<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class YearResource extends JsonResource
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
            'semesters' => SemesterResource::collection($this->whenLoaded('semesters')),
            'status' => $this->status,
            'is_approved' => $this->is_approved,
            'is_completed' => $this->is_completed,
            'created_at' => $this->created_at,
        ];
    }
}
