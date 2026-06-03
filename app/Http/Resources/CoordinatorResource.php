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
            // Identity fields live on the related user.
            'name' => $this->user?->name,
            'userId' => $this->user?->user_uuid,
            'email' => $this->user?->email,
            'phone' => $this->user?->phone,
            'center' => new CenterResource($this->whenLoaded('center')),
            'user' => new UserResource($this->whenLoaded('user')),
            'profileImg' => $this->user?->profile_img ? Storage::url($this->user->profile_img) : null,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
