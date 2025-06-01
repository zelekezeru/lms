<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CenterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'address' => $this->address,
            'status' => $this->status,
            'coordinator' => new CoordinatorResource($this->whenLoaded('coordinator')),
            'students' => StudentResource::collection($this->whenLoaded('students')),
            'students_count' => $this->whenLoaded('students', function () {
                return $this->students->count();
            }),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
