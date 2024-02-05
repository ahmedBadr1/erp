<?php

namespace App\Http\Resources\Inventory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if($this->items_quantity){
            $total =  formatMoney( ((int)$this->items_quantity ?? 0) *  ((int)$this->items_price ?? 0)) ;
        }
        return [
            'id' =>  $this->whenNotNull($this->id),
            'name' => $this->whenNotNull($this->name),
            'short_name' => $this->whenNotNull($this->short_name),
            'part_number' => $this->whenNotNull($this->part_number),
            'sku' => $this->whenNotNull($this->sku),
            'origin_number' =>$this->whenNotNull($this->origin_number),
            's_price' => $this->whenNotNull($this->s_price),
            'd_price' => $this->whenNotNull($this->d_price),
            'sd_price' =>$this->whenNotNull($this->sd_price),
            'min_price' =>$this->whenNotNull($this->min_price),
            'ref_price' =>$this->whenNotNull($this->ref_price),
            'avg_cost' =>$this->whenNotNull($this->avg_cost),
            'profit_margin' => $this->whenNotNull($this->profit_margin),
            'warranty' => $this->whenNotNull($this->warranty),
            'expire_date' => $this->whenNotNull($this->expire_date),
            'barcode' =>$this->whenNotNull($this->barcode),
            'hs_code' => $this->whenNotNull($this->hs_code),
            'weight' => $this->whenNotNull($this->weight),
            'width' => $this->whenNotNull($this->width),
            'length' =>$this->whenNotNull($this->length),
            'height' => $this->whenNotNull($this->height),
            'max_limit' => $this->whenNotNull($this->max_limit),
            'min_limit' => $this->whenNotNull($this->min_limit),
            'require_barcode' =>$this->whenNotNull($this->require_barcode),
            'repeat_barcode' => $this->whenNotNull($this->repeat_barcode),
            'negative_stock' => $this->whenNotNull($this->negative_stock),
            'can_be_sold' => $this->whenNotNull($this->can_be_sold),
            'can_be_purchased' => $this->whenNotNull($this->can_be_purchased),
            'returnable' => $this->whenNotNull($this->returnable),
            'active' =>$this->whenNotNull($this->active),
            'warehouse' => $this->whenLoaded('warehouse'),
            'unit' => $this->whenLoaded('unit'),
            'brand' => $this->whenLoaded('brand'),
            'taxes' => $this->whenLoaded('taxes'),
            'supplier' => $this->whenLoaded('supplier'),
            'responsible' => $this->whenLoaded('responsible'),

            'category' => $this->whenLoaded('category'),
            'items_value' => $total ?? 0,
//            'items_quantity' =>( (int)$this->items_quantity ?? 0),
//            'items_price' => ((int)$this->items_price ?? 0),
//            'expired_items_quantity' => (int)$this->expired_items_quantity ?? 0,
//            'expired_items_price' => (int)$this->expired_items_price ?? 0,

        ];
    }
}
