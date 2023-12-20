<?php

namespace App\Http\Resources\System;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' =>$this->id,
            'type' =>$this->type,
            'title' =>$this->title,
            'data' =>$this->data,
            'priority' =>$this->priority,
            'note' =>$this->note,
            'response' =>$this->response,
            'resolved' =>$this->resolved,

            'status' => new StatusResource($this->whenLoaded('status')),

        ];
    }
}
