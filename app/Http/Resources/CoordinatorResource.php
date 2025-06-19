<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CoordinatorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'userId' => $this->user->user_uuid,
            'email' => $this->email,
            'center' => new CenterResource($this->whenLoaded('center')),
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),

            'user' => new UserResource($this->whenLoaded('user')),

            'profileImg' => Storage::url($this->user->profile_img),
        ];
    }
}
