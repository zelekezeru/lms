<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class InstructorResource extends JsonResource
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
            'name' => $this->user->name,
            'email' => $this->user->email,
            'jobPosition'  => $this->specialization,
            'bio'  => $this->bio,
            'status'  => $this->status,
            'specialization'  => $this->specialization,
            'employmentType' => $this->employment_type,
            'hireDate'  => $this->hire_date,
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'user' => $this->whenLoaded('user'),
            'profileImg'  => Storage::url($this->user->profile_img),
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];

    }
}
