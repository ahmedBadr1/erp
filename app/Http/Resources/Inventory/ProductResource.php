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
            'product_id'=> $this->id  ,
            'name'=> $this->name  ,
            'code'=> $this->code  ,
            'last_price'=> $this->last_price  ,
            'category'=> $this->category?->name  ,
        ];
    }
}
