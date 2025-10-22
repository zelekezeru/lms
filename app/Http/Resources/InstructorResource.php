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
            'user' => new UserResource($this->whenLoaded('user')),
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'user_id' => $this->user_id,

            // Safely access user fields to avoid "property on null" errors
            'email' => optional($this->user)->email,
            'ContactPhone' => optional($this->user)->phone,
            'HireDate' => $this->hire_date,
            'bio' => $this->bio,
            'status' => $this->status,
            'specialization' => $this->specialization,
            'employmentType' => $this->employment_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Only build a URL if profile_img is present
            'profileImg' => $this->when(optional($this->user)->profile_img, function () {
                return Storage::url($this->user->profile_img);
            }, null),

            'courses' => CourseResource::collection($this->whenLoaded('courses')),
            'classSchedules' => ClassScheduleResource::collection($this->whenLoaded('classSchedules')),
            'classSessions' => ClassSessionResource::collection($this->whenLoaded('classSessions')),
            'sections' => $this->whenLoaded('courseOfferings', function () {
                return $this->courseOfferings->map(function ($courseOffering) {
                    return new SectionResource($courseOffering->section);
                });
            }),
        ];
    }
}
