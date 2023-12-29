<?php

namespace App\Http\Resources\System;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class InvitationResource extends JsonResource
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
            'email' => $this->email,
            'expire_at' => $this->expire_at?->diffForHumans(),
            'registered_at' => $this->registered_at?->diffForHumans(),
            'expire' => Carbon::parse($this->expire_at) < Carbon::now() ,
            'sender' => $this->whenLoaded('sender'),
            'role' => $this->whenLoaded('role')
        ];
    }
}
