<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatusResource extends JsonResource
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
            'is_active' => $this->is_active,
            'is_deleted' => $this->is_deleted,
            'is_approved' => $this->is_approved,
            'is_completed' => $this->is_completed,
            'is_verified' => $this->is_verified,
            'is_enrolled' => $this->is_enrolled,
            'is_graduated' => $this->is_graduated,
            'is_scholarship' => $this->is_scholarship,
            'is_scholarship_approved' => $this->is_scholarship_approved,
            'is_scholarship_requested' => $this->is_scholarship_requested,
            'is_scholarship_rejected' => $this->is_scholarship_rejected,

            'created_by_name' => $this->created_by_name,
            'updated_by_name' => $this->updated_by_name,
            'deleted_by_name' => $this->deleted_by_name,
            'approved_by_name' => $this->approved_by_name,
            'completed_by_name' => $this->completed_by_name,
            'verified_by_name' => $this->verified_by_name,
            'enrolled_by_name' => $this->enrolled_by_name,
            'graduated_by_name' => $this->graduated_by_name,
            'scholarship_by_name' => $this->scholarship_by_name,
            'scholarship_approved_by_name' => $this->scholarship_approved_by_name,
            'scholarship_requested_by_name' => $this->scholarship_requested_by_name,
            'scholarship_reason' => $this->scholarship_reason,
            'scholarship_rejected_reason' => $this->scholarship_rejected_reason,
            'scholarship_rejected_by_name' => $this->scholarship_rejected_by_name,

            'approved_at' => $this->approved_at,
            'completed_at' => $this->completed_at,
            'verified_at' => $this->verified_at,
            'enrolled_at' => $this->enrolled_at,
            'graduated_at' => $this->graduated_at,
            'scholarship_at' => $this->scholarship_at,
            'scholarship_approved_at' => $this->scholarship_approved_at,
            'scholarship_requested_at' => $this->scholarship_requested_at,
            'scholarship_rejected_at' => $this->scholarship_rejected_at,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,

            'user' => $this->whenLoaded('user'),
            'student' => $this->whenLoaded('student'),
        ];
    }
}
