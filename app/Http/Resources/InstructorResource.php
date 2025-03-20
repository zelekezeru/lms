<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class InsructoryResource extends JsonResource
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
            'user_uuid'  => $this->user->user_uuid,
            'name'  => $this->user->name,
            'email' => $this->user->email,
            'specialization'  => $this->specialization,
            'bio'  => $this->bio,
            'status'  => $this->status,
            'enrolmentType' => $this->enrolment_type,
            'hireDate'  => $this->hire_date,

            'department' => new DepartmentResource($this->whenLoaded('department')),
            'userRole' => $this->user->roles()->first()->name,
            'profileImg'  => Storage::url($this->user->profile_img),
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
