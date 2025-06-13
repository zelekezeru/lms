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
            'userId' => $this->user_uuid,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,

            'profileImg' => Storage::url($this->profile_img),

            'roles' => $this->whenLoaded('roles'),
            'default_password' => $this->default_password,
            'userDocument' => new UserDocumentResource($this->whenLoaded('userDocument')),
        ];
    }
}
