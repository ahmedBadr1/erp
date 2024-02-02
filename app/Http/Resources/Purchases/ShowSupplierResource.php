<?php

namespace App\Http\Resources\Purchases;

use App\Http\Resources\NameResource;
use App\Http\Resources\System\AccessResource;
use App\Http\Resources\System\AddressResource;
use App\Http\Resources\System\ContactResource;
use App\Http\Resources\System\TagResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowSupplierResource extends JsonResource
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
            'name' => $this->name,
            'code' => $this->code,
            'account' => new NameResource($this->whenLoaded('account')),

            'debit_limit' => $this->debit_limit,
            'warranty' => $this->warranty,
            'tax_number' => $this->tax_number,
            'registration_number' => $this->registration_number,
            'payment_method' => $this->payment_method,

            'contact' => new ContactResource($this->whenLoaded('lastContact')),
            'address' => new AddressResource($this->whenLoaded('lastAddress')),
            'accesses' => AccessResource::collection($this->whenLoaded('accesses')),

            'tags' => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}
