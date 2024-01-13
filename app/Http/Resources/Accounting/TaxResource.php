<?php

namespace App\Http\Resources\Accounting;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' =>$this->id,
            'name' =>$this->name,
            'code' =>$this->code,
            'rate' =>$this->rate,
            'scope' => $this->scope,
            'exclusive' =>$this->exclusive,
            'active' =>$this->active,
            'account' =>  new AccountResource($this->whenLoaded('account')),

        ];
    }
}
