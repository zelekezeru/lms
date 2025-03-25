<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class EmployeeResource extends JsonResource
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
            'name'  => $this->user->name,
            'email' => $this->user->email,
            'userRole' => $this->user->roles()->first()->name,
            'jobPosition'  => $this->job_position,
            'employmentType' => $this->employment_type,
            'officeHours'  => $this->office_hours,
            'profileImg'  => Storage::url($this->user->profile_img),
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
