<?php

namespace App\Http\Resources\Inventory;

use App\Http\Resources\Accounting\CostCenterResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCategoryResource extends JsonResource
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
            'color' => $this->credit,
            'parent_id' => $this->parent_id,
            'active' => $this->active,
            'children_count' => $this->whenCounted('children'),
            'children' => self::collection($this->whenLoaded('children')),
            'products' => ProductChartResource::collection($this->whenLoaded('products')),
        ];
    }
}
