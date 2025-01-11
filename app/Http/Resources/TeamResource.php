<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'captain'       => new UserResource($this->whenLoaded('captain')),
            'members'       => UserResource::collection($this->whenLoaded('members')),
            'members_count' => $this->members_count ?? $this->members->count(),
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
