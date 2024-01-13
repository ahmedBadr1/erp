<?php

namespace App\Http\Resources\Accounting;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
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
            'code' => $this->code,
            'symbol' => $this->symbol,
            'ex_rate' => $this->ex_rate,
            'last_rate' => $this->last_rate,
            'sub_unit' => $this->sub_unit,
            'order' => $this->order_id,
            'active' => $this->active,
            'gainAccount' => new AccountResource($this->whenLoaded('gainAccount')),
            'lossAccount' => new AccountResource($this->whenLoaded('lossAccount')),
        ];
    }
}
