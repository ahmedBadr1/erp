<?php

namespace App\Http\Resources\Accounting;

use App\Http\Resources\System\AccessResource;
use App\Http\Resources\System\AddressResource;
use App\Http\Resources\System\ContactResource;
use App\Http\Resources\System\StatusResource;
use App\Http\Resources\System\TagResource;
use App\Models\System\Address;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->whenNotNull($this->code),
            'name' => $this->whenNotNull($this->name),
            'type_code' => $this->whenNotNull($this->type_code),
            'credit' => $this->whenNotNull($this->credit),
            'description' => $this->whenNotNull($this->description),
            'credit_limit' => $this->whenNotNull($this->credit_limit),
            'debit_limit' => $this->whenNotNull($this->debit_limit),
            'c_opening' =>(float) $this->d_opening ,
            'd_opening' =>(float) $this->d_opening ,
            'opening_date' => $this->whenNotNull($this->opening_date),
            'currency_id' => $this->whenNotNull($this->currency_id),

            'c_balance' =>(float)  $this->c_balance ,
            'd_balance' => (float) $this->d_balance ,
            'active' => $this->whenNotNull($this->active),
            'system' => $this->whenNotNull($this->system),

            'credit_sum' => (float)$this->credit_sum ,
            'debit_sum' => (float) $this->debit_sum ,
            'period_credit_sum' => (float)$this->period_credit_sum ,
            'period_debit_sum' => (float)$this->period_debit_sum ,

//            'node_id' => $this->'node_id',
//            'currency_id' => $this->currency_id,
//            'status_id' => $this->status_id,
            'costCenter' => new CostCenterResource($this->whenLoaded('costCenter')),

            'entries' => EntryResource::collection($this->whenLoaded('entries')),
//            'transactions' =>  TransactionResource::collection($this->whenLoaded('transactions')),
            'currency' => new CurrencyResource($this->whenLoaded('currency')),
            'node' => new NodeResource($this->whenLoaded('node')),
            'status' => new StatusResource($this->whenLoaded('status')),

            'contact' =>new ContactResource($this->whenLoaded('lastContact')),
            'address' => new AddressResource($this->whenLoaded('lastAddress')),
            'accesses' => AccessResource::collection($this->whenLoaded('accesses')),

            'userAccesses' =>$this->whenLoaded('userAccesses'),

            'tags' => TagResource::collection($this->whenLoaded('tags')),

        ];
    }
}
