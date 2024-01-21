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
            'code' => $this->code,
            'name' => $this->name,
            'type_code' => $this->type_code,
            'credit' => $this->credit,
            'description' => $this->description,
            'credit_limit' => $this->credit_limit,
            'debit_limit' => $this->debit_limit,
            'c_opening' => $this->d_opening ?? 0,
            'd_opening' => $this->d_opening ?? 0,
            'opening_date' => $this->opening_date,//->format('m/d/Y'),
            'currency_id' => $this->currency_id,

            'active' => $this->system,
            'system' => $this->system,

            'credit_sum' => (int)$this->credit_sum ?? 0,
            'debit_sum' => (int)$this->debit_sum ?? 0,
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

            'tags' => TagResource::collection($this->whenLoaded('tags')),

        ];
    }
}
