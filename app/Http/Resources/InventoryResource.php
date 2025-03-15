<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
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
            'quantity' => $this->quantity,
            'unitPrice' => $this->unit_price,
            'description' => $this->description,
            'status' => $this->status,
            'category' => $this->whenLoaded('category'),
            'supplier' => $this->whenLoaded('supplier'),
        ];
    }
}
