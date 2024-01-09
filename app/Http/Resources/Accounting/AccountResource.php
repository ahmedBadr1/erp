<?php

namespace App\Http\Resources\Accounting;

use App\Http\Resources\System\CurrencyResource;
use App\Http\Resources\System\StatusResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
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
            'credit' => $this->credit,
            'description' => $this->description,
            'credit_limit' => $this->credit_limit,
            'debit_limit' => $this->debit_limit,
            'c_opening' => $this->d_opening ?? 0,
            'd_opening' => $this->d_opening ?? 0,
            'opening_balance_date' => $this->opening_balance_date,//->format('m/d/Y'),
            'active' => $this->system,
            'system' => $this->system,

            'credit_sum' => (int)$this->credit_sum ?? 0,
            'debit_sum' => (int)$this->debit_sum ?? 0,
//            'node_id' => $this->'node_id',
//            'currency_id' => $this->currency_id,
//            'status_id' => $this->status_id,

            'entries' => EntryResource::collection($this->whenLoaded('entries')),
//            'transactions' =>  TransactionResource::collection($this->whenLoaded('transactions')),
            'currency' => new CurrencyResource($this->whenLoaded('currency')),
            'node' => new NodeResource($this->whenLoaded('node')),
            'status' => new StatusResource($this->whenLoaded('status')),
        ];
    }
}
