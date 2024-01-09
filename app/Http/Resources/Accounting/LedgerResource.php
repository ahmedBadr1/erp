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
            'type' => $this->type,
            'amount' => $this->amount,
            'reference' => $this->reference,
            'description' => $this->description,
            'posted' => $this->post,
            'locked' => $this->locked,
            'due' => $this->due->format('d/m/Y'),
            'user' => new UserResource ($this->whenLoaded('user')),
            'entries' => EntryResource::collection($this->whenLoaded('entries')),
            'transactions' => TransactionResource::collection($this->whenLoaded('transactions')),
            'accounts' => AccountResource::collection($this->whenLoaded('accounts')),
        ];
    }
}
