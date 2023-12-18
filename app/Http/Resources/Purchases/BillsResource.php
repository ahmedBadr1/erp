<?php

namespace App\Http\Resources\Purchases;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillsResource extends JsonResource
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
            'code' => $this->code,
            'vendor' => $this->vendor?->name,
            'status' => $this->status?->name,
            'billed_at' => $this->billed_at,
            'billed' => $this->billed_at?->diffForHumans(),
            'due' => $this->due_at?->diffForHumans(),
            'due_at' => $this->due_at,
            'total' => $this->name,
        ];
    }
}
