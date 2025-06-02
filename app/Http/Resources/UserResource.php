<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'profileImg' => Storage::url($this->profile_img),
            'user_uuid' => $this->user_uuid,
            'tenant_id' => $this->tenant_id,
            'userRole' => $this->user ? $this->user->roles()->first()->name : null,
            'default_password' => $this->default_password,
            'userDocument' => new UserDocumentResource($this->whenLoaded('userDocument')),
        ];
    }
}
