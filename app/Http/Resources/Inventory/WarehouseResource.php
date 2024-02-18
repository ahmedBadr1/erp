<?php

namespace App\Http\Resources\Inventory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' =>  $this->whenNotNull($this->id),
            'name' => $this->whenNotNull($this->name),
            'type' => $this->whenNotNull($this->type),
            'description' => $this->whenNotNull($this->description),
            'is_rma' => $this->whenNotNull($this->is_rma),
            'is_rented' => $this->whenNotNull($this->is_rented),
            'has_security' => $this->whenNotNull($this->has_security),
            'active' => $this->whenNotNull($this->active),
            'balance' => $this->balance ?? 0,

            'products_count' => $this->whenCounted('products') ,
            'manager' => $this->whenLoaded('manager'),

        ];
    }
}
