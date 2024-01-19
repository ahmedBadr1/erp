<?php

namespace App\Http\Resources\Accounting;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LedgerResource extends JsonResource
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
            'code' => $this->code,
            'amount' => $this->amount,
            'description' => $this->description,
            'ex_rate' => $this->ex_rate,
            'posted' => $this->post,
            'locked' => $this->whenNotNull('locked'),
            'due' => $this->due->format('H:m d/m/Y'),
            'responsible' => new UserResource ($this->whenLoaded('responsible')),
            'creator' => new UserResource ($this->whenLoaded('responsible')),
            'editor' => new UserResource ($this->whenLoaded('responsible')),
            'currency' => new CurrencyResource($this->whenLoaded('currency')),

            'entries' => EntryResource::collection($this->whenLoaded('entries')),
            'transactions' => TransactionResource::collection($this->whenLoaded('transactions')),
            'accounts' => AccountResource::collection($this->whenLoaded('accounts')),
        ];
    }
}
