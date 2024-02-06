<?php

namespace App\Http\Resources\Inventory;

use App\Http\Resources\NameResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductStockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        if($this->items_quantity){
//            $total =  formatMoney( ((int)$this->items_quantity ?? 0) *  ((int)$this->items_price ?? 0)) ;
//        }
        return [
            'id' =>  $this->whenNotNull($this->id),
            'name' => $this->whenNotNull($this->name),
            'avg_cost' =>$this->whenNotNull($this->avg_cost),
            'part_number' =>$this->whenNotNull($this->part_number),
            'global_balance' =>$this->whenNotNull($this->global_balance),

            'stocks' =>  StockResource::collection($this->whenLoaded('stocks')),
            'unit' => $this->whenLoaded('unit'),
            'category' => new NameResource($this->whenLoaded('category')),


//            'items_quantity' =>( (int)$this->items_quantity ?? 0),
//            'items_price' => ((int)$this->items_price ?? 0),
//            'expired_items_quantity' => (int)$this->expired_items_quantity ?? 0,
//            'expired_items_price' => (int)$this->expired_items_price ?? 0,

        ];
    }
}
