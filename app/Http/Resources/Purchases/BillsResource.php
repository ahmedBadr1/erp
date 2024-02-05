<?php

namespace App\Http\Resources\Purchases;

use App\Http\Resources\Accounting\TaxResource;
use App\Http\Resources\NameResource;
use App\Http\Resources\System\ModelGroupResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillsResource extends JsonResource
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
            'code' =>  $this->whenNotNull($this->code),
            'paper_ref' => $this->whenNotNull($this->paper_ref),
            'date' =>  $this->whenNotNull($this->date),
            'deliver_at' => $this->whenNotNull($this->deliver_at),

            'gross_total' =>  $this->whenNotNull($this->gross_total),
            'discount' => $this->whenNotNull($this->discount),
            'sub_total' =>  $this->whenNotNull($this->sub_total),
            'tax_total' =>  $this->whenNotNull($this->tax_total),
            'total' => $this->whenNotNull($this->total),

            'items' => BillItemRequest::collection($this->whenLoaded('items')),
            'treasury' => new NameResource($this->whenLoaded('treasury')),
            'secondParty' => new NameResource($this->whenLoaded('second_party')),
            'warehouse' => new NameResource($this->whenLoaded('warehouse')),
            'currency' => new NameResource($this->whenLoaded('currency')),
            'tax' => new TaxResource($this->whenLoaded('tax')),

            'responsible' => new NameResource($this->whenLoaded('responsible')),

            'group' => new ModelGroupResource($this->whenLoaded('group')),
        ];
    }
}
