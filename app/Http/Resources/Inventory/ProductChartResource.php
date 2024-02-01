<?php

namespace App\Http\Resources\Inventory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductChartResource extends JsonResource
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
            'part_number' => $this->part_number,
            'sku' => $this->sku,

            'unit' => $this->whenLoaded('unit'),
            'brand' => $this->whenLoaded('brand'),
            'responsible' => $this->whenLoaded('responsible'),

            'category' => $this->whenLoaded('category'),
        ];
    }
}
