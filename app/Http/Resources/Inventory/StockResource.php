<?php

namespace App\Http\Resources\Inventory;

use App\Http\Resources\NameResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
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
            'balance' =>  $this->whenNotNull($this->balance),
            'warehouse_id' =>  $this->whenNotNull($this->warehouse_id),
            'product_id' =>  $this->whenNotNull($this->product_id),
            'warehouse' => new NameResource ($this->whenLoaded('warehouse')),
            'product' => new NameResource ($this->whenLoaded('product')),


        ];
    }
}
