<?php

namespace App\Http\Resources\Accounting;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EntryResource extends JsonResource
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
            'amount' => $this->whenNotNull($this->amount),
            'credit' => $this->whenNotNull($this->credit),
            'comment' => $this->whenNotNull($this->comment),
            'posted' => $this->whenNotNull($this->posted),
            'locked' => $this->whenNotNull($this->locked),

            'created_at' => $this->created_at->format('d-m-Y h:i a'),

            'account' =>  new AccountChartResource($this->whenLoaded('account')),
            'ledger' =>  new LedgerResource($this->whenLoaded('ledger')),
            'costCenter' =>  new CostCenterResource($this->whenLoaded('costCenter')),
//            'client' =>  new ClientResource($this->whenLoaded('client')),
//            'supplier' =>  new SupplierResource($this->whenLoaded('supplier')),

        ];
    }
}
