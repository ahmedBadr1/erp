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
//            'id' => $this->id,
            'credit' => $this->credit,
            'amount' => $this->amount,
            'comment' => $this->comment,
            'posted' => $this->post,
            'locked' => $this->locked,
            'created_at' => $this->created_at->format('d/m/Y'),
            'account' =>  new AccountChartResource($this->whenLoaded('account')),
            'transaction' =>  new TransactionResource($this->whenLoaded('transaction')),
            'costCenter' =>  new CostCenterResource($this->whenLoaded('costCenter')),
//            'client' =>  new ClientResource($this->whenLoaded('client')),
//            'supplier' =>  new SupplierResource($this->whenLoaded('supplier')),

        ];
    }
}
