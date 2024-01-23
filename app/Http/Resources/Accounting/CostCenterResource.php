<?php

namespace App\Http\Resources\Accounting;

use App\Http\Resources\System\AccessResource;
use App\Http\Resources\System\AddressResource;
use App\Http\Resources\System\ContactResource;
use App\Http\Resources\System\StatusResource;
use App\Http\Resources\System\TagResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CostCenterResource extends JsonResource
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
            'description' => $this->description,
            'active' => $this->active,
            'system' => $this->system,

            'children_count' => $this->whenCounted('children'),
            'children' => self::collection($this->whenLoaded('children')),
//            'entries' => EntryResource::collection($this->whenLoaded('entries')),
            'transactions' =>  TransactionResource::collection($this->whenLoaded('transactions')),
//            'currency' => new CurrencyResource($this->whenLoaded('currency')),

            'node' => new CostCenterNodeResource($this->whenLoaded('node')),
            'status' => new StatusResource($this->whenLoaded('status')),

//            'contact' =>new ContactResource($this->whenLoaded('lastContact')),
//            'address' => new AddressResource($this->whenLoaded('lastAddress')),
            'accesses' => AccessResource::collection($this->whenLoaded('accesses')),

            'userAccesses' =>$this->whenLoaded('userAccesses'),

            'tags' => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}
