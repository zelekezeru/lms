<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentTypeResource extends JsonResource
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
            'type' => $this->type,
            'language' => $this->language,
            'amount' => $this->amount,
            'duration' => $this->duration,
            'payment_category_id' => $this->payment_category_id,
            'is_active' => $this->is_active,
            'tenant_id' => $this->tenant_id,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            
            'studyMode' => new StudyModeResource($this->whenLoaded('studyMode')),            
        ];
    }
}
