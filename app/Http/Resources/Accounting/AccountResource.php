<?php

namespace App\Http\Resources\Accounting;

use App\Http\Resources\System\CurrencyResource;
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
            'opening_balance' => $this->opening_balance,
            'opening_balance_date' => $this->opening_balance_date,//->format('m/d/Y'),
            'system' => $this->system,
            'active' => $this->active,
            'credit_sum' =>(int)  $this->credit_sum ?? 0 ,
            'debit_sum' => (int) $this->debit_sum ?? 0,
//            'acc_category_id' => $this->acc_category_id,
//            'currency_id' => $this->currency_id,
//            'status_id' => $this->status_id,
            'entries' =>  EntryResource::collection($this->whenLoaded('entries')),
            'transactions' =>  TransactionResource::collection($this->whenLoaded('transactions')),
            'currency' =>  new CurrencyResource($this->whenLoaded('currency')),
            'category' =>  new AccCategoryResource($this->whenLoaded('category')),
            'status' =>  new CurrencyResource($this->whenLoaded('status')),
        ];
    }
}
