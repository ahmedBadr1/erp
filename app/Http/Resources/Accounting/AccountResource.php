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
            'opening_balance_date' => $this->opening_balance_date,
            'system' => $this->system,
            'active' => $this->active,
            'acc_category_id' => $this->acc_category_id,
            'currency_id' => $this->currency_id,
            'status_id' => $this->status_id,
            'currency' =>  new CurrencyResource($this->whenLoaded('currency')),
            'category' =>  new AccCategoryResource($this->whenLoaded('category')),
            'status' =>  new CurrencyResource($this->whenLoaded('status')),
        ];
    }
}
