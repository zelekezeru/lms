<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'payment_date' => $this->payment_date,
            'total_amount' => $this->total_amount,
            'description' => $this->description,
            'status' => $this->status,
            'payment_reference' => $this->payment_reference,
            'tenant_id' => $this->tenant_id,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'is_active' => $this->is_active,
            'is_deleted' => $this->is_deleted,

            'paymentMethod' => $this->whenLoaded('paymentMethod'),
            'paymentType' => $this->whenLoaded('paymentType'),
            'semester' => $this->whenLoaded('semester'),

        ];
    }
}
