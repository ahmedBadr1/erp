<?php

namespace App\Http\Resources\Inventory;

use App\Http\Resources\NameResource;
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
            'id' =>  $this->whenNotNull($this->id),
            'name' => $this->whenNotNull($this->name),
            'short_name' => $this->whenNotNull($this->short_name),
            'part_number' => $this->whenNotNull($this->part_number),
            'sku' => $this->whenNotNull($this->sku),
            'location' =>$this->whenNotNull($this->location),
            'oe_number' =>$this->whenNotNull($this->oe_number),
            's_price' => $this->whenNotNull($this->s_price),
            'd_price' => $this->whenNotNull($this->d_price),
            'sd_price' =>$this->whenNotNull($this->sd_price),
            'min_price' =>$this->whenNotNull($this->min_price),
            'ref_price' =>$this->whenNotNull($this->ref_price),
            'pur_price' =>$this->whenNotNull($this->pur_price),
            'last_cost' =>$this->whenNotNull($this->last_cost),
            'avg_cost' =>$this->whenNotNull($this->avg_cost),
            'fifo' =>$this->whenNotNull($this->fifo),
            'lifo' =>$this->whenNotNull($this->lifo),
            'profit_margin' => $this->whenNotNull($this->profit_margin),
            'warranty' => $this->whenNotNull($this->warranty),
            'valid_to' => $this->whenNotNull($this->valid_to),
            'product_category_id' => $this->whenNotNull($this->product_category_id),

            'e_code' => $this->whenNotNull($this->e_code),
            'e_code_type' => $this->whenNotNull($this->e_code_type),

            'barcode' =>$this->whenNotNull($this->barcode),
            'hs_code' => $this->whenNotNull($this->hs_code),
            'weight' => $this->whenNotNull($this->weight),
            'width' => $this->whenNotNull($this->width),
            'length' =>$this->whenNotNull($this->length),
            'height' => $this->whenNotNull($this->height),
            'max_limit' => $this->whenNotNull($this->max_limit),
            'min_limit' => $this->whenNotNull($this->min_limit),
            'reorder_limit' => $this->whenNotNull($this->reorder_limit),
            'track_stock' => $this->whenNotNull($this->track_stock),

                        'use_batch_number' =>$this->whenNotNull($this->use_batch_number),

            'require_serial' =>$this->whenNotNull($this->require_serial),
            'repeat_serial' => $this->whenNotNull($this->repeat_serial),
            'negative_stock' => $this->whenNotNull($this->negative_stock),
            'can_be_sold' => $this->whenNotNull($this->can_be_sold),
            'can_be_purchased' => $this->whenNotNull($this->can_be_purchased),
            'returnable' => $this->whenNotNull($this->returnable),
            'active' =>$this->whenNotNull($this->active),
            'stocks_balance' =>$this->whenNotNull($this->stocks_balance),

            'warehouse' => new NameResource( $this->whenLoaded('warehouse')),
            'unit' => new NameResource($this->whenLoaded('unit')),
            'brand' =>new NameResource( $this->whenLoaded('brand')),
            'taxes' => new NameResource($this->whenLoaded('taxes')),
            'supplier' => new NameResource($this->whenLoaded('supplier')),
            'responsible' => new NameResource($this->whenLoaded('responsible')),
            'discounts' => $this->whenLoaded('discounts'),


            'category' => new NameResource($this->whenLoaded('category')),
            'balance' => (int) $this->balance ,
            'total_value' =>  formatMoney( (int)$this->balance  *  (int)$this->avg_cost )  ,
//            'items_quantity' =>( (int)$this->items_quantity ?? 0),
//            'items_price' => ((int)$this->items_price ?? 0),
//            'expired_items_quantity' => (int)$this->expired_items_quantity ?? 0,
//            'expired_items_price' => (int)$this->expired_items_price ?? 0,

        ];
    }
}
