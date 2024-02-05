<?php

namespace App\Http\Resources\System;

use App\Http\Resources\NameResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModelGroupResource extends JsonResource
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


            'po' => NameResource::collection($this->whenLoaded('po')),
            'so' => NameResource::collection($this->whenLoaded('so')),


            'firstTransaction' => new NameResource($this->whenLoaded('firstTransaction')),
            'lastTransaction' => new NameResource($this->whenLoaded('lastTransaction')),
            'firstCiTransaction' => new NameResource($this->whenLoaded('firstCiTransaction')),

        ];
    }
}
