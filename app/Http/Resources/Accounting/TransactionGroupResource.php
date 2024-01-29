<?php

namespace App\Http\Resources\Accounting;

use App\Http\Resources\Purchases\BillsResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionGroupResource extends JsonResource
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

            'entries' => EntryResource::collection($this->whenLoaded('entries')),
            'transactions' => TransactionResource::collection($this->whenLoaded('transactions')),
            'ledgers' => TransactionResource::collection($this->whenLoaded('transactions')),
            'accounts' => AccountResource::collection($this->whenLoaded('accounts')),

            'bills' => BillsResource::collection($this->whenLoaded('bills')),


            'firstTransaction' => new TransactionResource($this->whenLoaded('firstTransaction')),
            'lastTransaction' => new TransactionResource($this->whenLoaded('lastTransaction')),
            'firstCiTransaction' => new TransactionResource($this->whenLoaded('firstCiTransaction')),

        ];
    }
}
