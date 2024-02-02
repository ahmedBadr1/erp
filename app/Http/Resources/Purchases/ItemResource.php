<?php

namespace App\Http\Resources\Purchases;

use App\Http\Resources\Accounting\AccountChartResource;
use App\Http\Resources\Accounting\CostCenterResource;
use App\Http\Resources\Accounting\TransactionResource;
use App\Http\Resources\NameResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
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
            'name' => $this->product?->name,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'cost' => $this->cost,
            'comment' => $this->comment,
            'product' =>  new NameResource($this->whenLoaded('product')),
            'supplier' =>  new NameResource($this->whenLoaded('supplier')),
            'bill' =>  new BillsResource($this->whenLoaded('bill')),
            'warehouse' =>  new NameResource($this->whenLoaded('warehouse')),
        ];
    }
}
