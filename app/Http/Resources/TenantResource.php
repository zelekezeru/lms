<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenantResource extends JsonResource
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
            'email' => $this->email,
            'code' => $this->code,
            'phone' => $this->phone,
            'address' => $this->address,
            'password' => $this->password,
            'contact_person' => $this->contact_person,
            'contact_phone' => $this->contact_phone,
            'aggrement' => $this->aggrement,
            'status' => $this->status,
            'allowed' => $this->allowed,
            'paid' => $this->active,
        ];
    }
}
