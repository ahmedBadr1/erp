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
            'id' => $this->id,
            'name' => $this->product?->name,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'product' =>  new NameResource($this->whenLoaded('product')),
            'warehouse' =>  new NameResource($this->whenLoaded('warehouse')),
        ];
    }
}
