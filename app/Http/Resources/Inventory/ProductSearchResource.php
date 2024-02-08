<?php

namespace App\Http\Resources\Inventory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSearchResource extends JsonResource
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
            'name' => $this->whenNotNull($this->name),
//            'short_name' => $this->whenNotNull($this->short_name),
//            'part_number' => $this->whenNotNull($this->part_number),
            'tax_id' => $this->whenNotNull($this->tax_id),
            's_price' => $this->whenNotNull($this->s_price),
//            'd_price' => $this->whenNotNull($this->d_price),
//            'sd_price' => $this->whenNotNull($this->sd_price),
//            'min_price' => $this->whenNotNull($this->min_price),
//            'ref_price' => $this->whenNotNull($this->ref_price),

            'avg_cost' =>$this->whenNotNull($this->avg_cost),
//            'profit_margin' => $this->whenNotNull($this->profit_margin),

//            'barcode' =>$this->whenNotNull($this->barcode),
            'warehouse_balance' => $this->warehouse_balance ?? 0,
            'stocks_balance' =>$this->stocks_balance ?? 0,

            'warehouse' => $this->whenLoaded('warehouse'),
            'unit' => $this->whenLoaded('unit'),
            'brand' => $this->whenLoaded('brand'),
            'taxes' => $this->whenLoaded('taxes'),
            'supplier' => $this->whenLoaded('supplier'),
            'responsible' => $this->whenLoaded('responsible'),

            'category' => $this->whenLoaded('category'),
        ];
    }
}
