<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TournamentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'description'        => $this->description,
            'entry_fee'          => $this->entry_fee,
            'type'               => $this->type,
            'creator'            => new UserResource($this->whenLoaded('creator')),
            'participants_count' => $this->participants_count,
            'start_date'         => $this->start_date,
            'end_date'           => $this->end_date,
            'created_at'         => $this->created_at,
            'updated_at'         => $this->updated_at,
        ];
    }
}
