<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
            
            'password_changed' => $this->password_changed,
            'default_password' => $this->default_password,
            'password' => $this->password,
            'contact_person' => $this->contact_person,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email,
            'aggrement' => $this->aggrement,
            'allowed' => $this->allowed,
            'status' => $this->status,
            'paid' => $this->paid,
            'logo'  => $this->logo,
        ];
    }
}
