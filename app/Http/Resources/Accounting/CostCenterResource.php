<?php

namespace App\Http\Resources\Accounting;

use App\Http\Resources\System\CurrencyResource;
use App\Http\Resources\System\StatusResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CostCenterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'active' => $this->system,
            'system' => $this->system,

            'children_count' => $this->whenCounted('children'),
            'children' => self::collection($this->whenLoaded('children')),
//            'entries' => EntryResource::collection($this->whenLoaded('entries')),
            'transactions' =>  TransactionResource::collection($this->whenLoaded('transactions')),
//            'currency' => new CurrencyResource($this->whenLoaded('currency')),
//            'node' => new NodeResource($this->whenLoaded('node')),
//            'status' => new StatusResource($this->whenLoaded('status')),
        ];
    }
}
