<?php

namespace App\Http\Resources\Accounting;

use App\Http\Resources\System\ModelGroupResource;
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
            'id' =>  $this->whenNotNull($this->id),
            'code' =>  $this->whenNotNull($this->code),
            'type' =>  $this->whenNotNull($this->type),
            'amount' =>  $this->whenNotNull($this->amount),
            'note' => $this->whenNotNull($this->note),
            'paper_ref' =>  $this->whenNotNull($this->paper_ref),
            'document_no' =>  $this->whenNotNull($this->document_no),
            'posted' => $this->whenNotNull($this->posted),
            'locked' =>$this->whenNotNull($this->locked),
            'due' => $this->whenNotNull($this->due),
            'creator' => new UserResource ($this->whenLoaded('creator')),
            'editor' => new UserResource ($this->whenLoaded('editor')),
            'responsible' => new UserResource ($this->whenLoaded('responsible')),

            'entries' => EntryResource::collection($this->whenLoaded('entries')),
            'accounts' => AccountResource::collection($this->whenLoaded('accounts')),
            'ledger' => new LedgerResource($this->whenLoaded('ledger')),
            'group' => new ModelGroupResource($this->whenLoaded('group')),

            'firstParty' => new AccountChartResource($this->whenLoaded('firstParty')),
            'secondParty' => new AccountChartResource($this->whenLoaded('secondParty')),

        ];
    }
}
