<?php

namespace App\Http\Resources\Accounting;

use App\Http\Resources\NameResource;
use App\Http\Resources\System\ModelGroupResource;
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
            'id' => $this->whenNotNull($this->id),
            'code' => $this->whenNotNull($this->code),
            'amount' => $this->whenNotNull($this->amount),
            'note' => $this->whenNotNull($this->note),
            'paper_ref' => $this->whenNotNull($this->paper_ref),
            'system' => $this->system,
            'posted' =>$this->posted,
            'locked' =>$this->locked,
            'created_at' => $this->whenNotNull($this->due)  ,
            'responsible' => new NameResource($this->whenLoaded('responsible')),
            'creator' => new NameResource ($this->whenLoaded('creator')),
            'editor' => new NameResource ($this->whenLoaded('editor')),
            'currency' => new NameResource($this->whenLoaded('currency')),

            'entries' => EntryResource::collection($this->whenLoaded('entries')),
            'transactions' => TransactionResource::collection($this->whenLoaded('transactions')),
            'accounts' => AccountResource::collection($this->whenLoaded('accounts')),
            'group' => new ModelGroupResource($this->whenLoaded('group')),
            'transaction' => new TransactionResource($this->whenLoaded('transaction')),

            'firstTransaction' => new TransactionResource($this->whenLoaded('firstTransaction')),
            'lastTransaction' => new TransactionResource($this->whenLoaded('lastTransaction')),
            'firstCiTransaction' => new TransactionResource($this->whenLoaded('firstCiTransaction')),

        ];
    }
}
