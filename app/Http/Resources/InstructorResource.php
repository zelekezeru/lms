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
            'user_id' => $this->user_id,
            'tenant_id' => $this->tenant_id,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'ContactPhone' => $this->contact_phone,
            'bio' => $this->bio,
            'status' => $this->status,
            'specialization' => $this->specialization,
            'employmentType' => $this->employment_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'profileImg' => Storage::url($this->user->profile_img),

            'user' => $this->whenLoaded('user'),

            'userRole' => $this->user && $this->user->roles()->first() ? $this->user->roles()->first()->name : null,

            'courses' => CourseResource::collection($this->whenLoaded('courses')),
            'sections' => $this->whenLoaded('courseSectionAssignments', function () {
                return $this->courseSectionAssignments->map(function ($courseSectionAssignment) {
                    return new SectionResource($courseSectionAssignment->section);
                });
            }),
        ];

    }
}
