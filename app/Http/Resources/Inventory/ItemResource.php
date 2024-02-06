<?php

namespace App\Http\Resources\Inventory;

use App\Http\Resources\NameResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->whenNotNull($this->id),
            'name' => $this->whenNotNull($this->product?->name),

            'quantity' => $this->whenNotNull($this->quantity),
            'price' => $this->whenNotNull($this->price),
            'avg_cost' => $this->whenNotNull($this->avg_cost),
            'balance' => $this->whenNotNull($this->balance),
            'in' => $this->whenNotNull($this->in),
            'created_at' => $this->whenNotNull($this->created_at)?->format('d-m-Y h:i a'),

            'product' =>  new NameResource($this->whenLoaded('product')),
            'warehouse' =>  new NameResource($this->whenLoaded('warehouse')),
            'transaction' =>  new NameResource($this->whenLoaded('transaction')),
            'secondParty' =>  new NameResource($this->whenLoaded('second_party')),
        ];
    }
}
