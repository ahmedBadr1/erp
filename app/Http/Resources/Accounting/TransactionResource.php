<?php

namespace App\Http\Resources\Accounting;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'description' => $this->description,
            'paper_ref' => $this->paper_ref,
            'document_no' => $this->document_no,
            'posted' => $this->posted,
            'locked' => $this->locked,
            'due' => $this->due->format('d/m/Y'),
            'creator' => new UserResource ($this->whenLoaded('creator')),
            'editor' => new UserResource ($this->whenLoaded('editor')),
            'responsible' => new UserResource ($this->whenLoaded('responsible')),

            'entries' => EntryResource::collection($this->whenLoaded('entries')),
            'accounts' => AccountResource::collection($this->whenLoaded('accounts')),
            'ledger' => new LedgerResource($this->whenLoaded('ledger')),
            'firstParty' => new AccountChartResource($this->whenLoaded('firstParty')),
            'secondParty' => new AccountChartResource($this->whenLoaded('secondParty')),

        ];
    }
}
