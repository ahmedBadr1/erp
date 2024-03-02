<?php

namespace App\Http\Resources\Accounting;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NodeLedgerResource extends JsonResource
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
            'code' => $this->whenNotNull($this->code),
            'name' => $this->whenNotNull($this->name),
            'depth' => $this->whenNotNull($this->depth),
            'net_credit' => $this->net_credit ?? 0.000,
            'net_debit' => $this->net_debit ?? 0.000,
            'opening_credit' => (float) $this->opening_credit ,
            'opening_debit' =>  (float) $this->opening_debit ,
            'total_credit' =>  (float) $this->total_credit ,
            'total_debit' =>   (float) $this->total_debit ,
            'children_count' => $this->whenCounted('children'),
            'children' => self::collection($this->whenLoaded('children')),
            'accounts' => AccountResource::collection($this->whenLoaded('accounts')),
        ];
    }
}
