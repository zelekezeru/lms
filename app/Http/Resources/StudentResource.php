<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class StudentResource extends JsonResource
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
            'id_no' => $this->id_no,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'mobile_phone' => $this->mobile_phone,
            'office_phone' => $this->office_phone,
            'date_of_birth' => $this->date_of_birth,
            'marital_status' => $this->marital_status,

            'profileImg'  => Storage::url($this->user->profile_img),
            'user' => $this->whenLoaded('user'),
            'status' => $this->whenLoaded('status'),
            'church' => $this->whenLoaded('church'),

            'courses' => $this->whenLoaded('courses'),
            'program' => $this->whenLoaded('program'),
            'track' => $this->whenLoaded('track'),
            'section' => $this->whenLoaded('section'),
            'year' => $this->whenLoaded('year'),
            'semester' => $this->whenLoaded('semester'),
            'payments' => $this->whenLoaded('payments'),
            'created_by' => $this->whenLoaded('createdBy'),

        ];
    }
}
