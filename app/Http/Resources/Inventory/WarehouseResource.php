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
            'id' => $this->id ,
            'name' => $this->name ,
            'type' => $this->type ,
            'active' => $this->active ,
            'items_count' => $this->whenCounted('items') ,
            'manager' => $this->whenLoaded('manager'),
            'items_sum' =>(int)  $this->items_sum ?? 0 ,
            'expired_items_sum' => (int) $this->expired_items_sum ?? 0,

        ];
    }
}
