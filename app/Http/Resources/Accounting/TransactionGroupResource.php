<?php

namespace App\Http\Resources\Accounting;

use App\Http\Resources\NameResource;
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

//            'entries' => EntryResource::collection($this->whenLoaded('entries')),
            'transactions' => NameResource::collection($this->whenLoaded('transactions')),
            'invTransactions' => NameResource::collection($this->whenLoaded('invTransactions')),

            'ledgers' => NameResource::collection($this->whenLoaded('ledgers')),
//            'accounts' => AccountResource::collection($this->whenLoaded('accounts')),


            'bills' => NameResource::collection($this->whenLoaded('bills')),

            'firstTransaction' => new NameResource($this->whenLoaded('firstTransaction')),
            'lastTransaction' => new NameResource($this->whenLoaded('lastTransaction')),
            'firstCiTransaction' => new NameResource($this->whenLoaded('firstCiTransaction')),

        ];
    }
}
