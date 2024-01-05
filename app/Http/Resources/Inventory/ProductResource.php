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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'short_name' => $this->short_name,
            'code' => $this->code,
            'origin_number' => $this->origin_number,
            'type' => $this->type,
            'price' => $this->price,
            'd_price' => $this->d_price,
            'sd_price' => $this->sd_price,
            'min_price' => $this->min_price,
            'ref_price' => $this->ref_price,
            'avg_cost' => $this->avg_cost,
            'profit_margin' => $this->profit_margin,
            'warranty' => $this->warranty,
            'expire_date' => $this->expire_date,
            'barcode' => $this->barcode,
            'hs_code' => $this->hs_code,
            'weight' => $this->weight,
            'width' => $this->width,
            'length' => $this->length,
            'height' => $this->height,
            'max_limit' => $this->max_limit,
            'min_limit' => $this->min_limit,
            'require_barcode' => $this->require_barcode,
            'repeat_barcode' => $this->repeat_barcode,
            'negative_stock' => $this->negative_stock,
            'can_be_sold' => $this->can_be_sold,
            'can_be_purchased' => $this->can_be_purchased,
            'returnable' => $this->returnable,
            'active' => $this->active,
            'warehouse' => $this->whenLoaded('warehouse'),
            'unit' => $this->whenLoaded('unit'),
            'brand' => $this->whenLoaded('brand'),
            'taxes' => $this->whenLoaded('taxes'),
            'supplier' => $this->whenLoaded('supplier'),
            'responsible' => $this->whenLoaded('responsible'),

            'category' => $this->whenLoaded('category'),
            'items_count' => $this->whenCounted('items'),

            'items_sum' =>(int)  $this->items_sum ?? 0 ,
            'expired_items_sum' => (int) $this->expired_items_sum ?? 0,

        ];
    }
}
