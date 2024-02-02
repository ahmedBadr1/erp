<?php

namespace App\Http\Resources\Inventory;

use App\Http\Resources\Accounting\AccountChartResource;
use App\Http\Resources\Accounting\AccountResource;
use App\Http\Resources\Accounting\EntryResource;
use App\Http\Resources\Accounting\LedgerResource;
use App\Http\Resources\Accounting\TransactionGroupResource;
use App\Http\Resources\NameResource;
use App\Http\Resources\Purchases\ItemResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvTransactionResource extends JsonResource
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
            'due' => $this->whenNotNull($this->due),
            'accepted_at' => $this->whenNotNull($this->accepted_at),
            'pending' =>$this->whenNotNull($this->pending),




            'items' => ItemResource::collection($this->whenLoaded('items')),
            'group' => new TransactionGroupResource($this->whenLoaded('group')),
            'from' => new NameResource($this->whenLoaded('from')),
            'to' => new NameResource($this->whenLoaded('to')),
            'supplier' => new NameResource($this->whenLoaded('supplier')),
            'client' => new NameResource($this->whenLoaded('client')),
            'bill' => new NameResource($this->whenLoaded('bill')),
            'invoice' => new NameResource($this->whenLoaded('invoice')),


            'creator' => new NameResource ($this->whenLoaded('creator')),
            'editor' => new NameResource ($this->whenLoaded('editor')),
            'responsible' => new NameResource ($this->whenLoaded('responsible')),

        ];
    }
}
