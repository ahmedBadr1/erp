<?php

namespace App\Http\Resources\Inventory;

use App\Http\Resources\NameResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseResource extends JsonResource
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
            'name' => $this->whenNotNull($this->name),
            'type' => $this->whenNotNull($this->type),
            'description' => $this->whenNotNull($this->description),
            'is_rma' => $this->whenNotNull($this->is_rma),
            'is_rented' => $this->whenNotNull($this->is_rented),
            'has_security' => $this->whenNotNull($this->has_security),

            'account_id' => $this->account_id,
            'p_account_id' => $this->p_account_id,
            'pr_account_id' => $this->pr_account_id,
            'pd_account_id' => $this->pd_account_id,
            's_account_id' => $this->s_account_id,
            'sr_account_id' => $this->sr_account_id,
            'sd_account_id' => $this->sd_account_id,
            'ss_account_id' => $this->ss_account_id,
            'cog_account_id' => $this->cog_account_id,
            'or_account_id' => $this->or_account_id,

            'active' => $this->whenNotNull($this->active),
            'balance' => $this->balance ?? 0,


            'products_count' => $this->whenCounted('products'),
            'manager' => new NameResource($this->whenLoaded('manager')),
            'account' => new NameResource($this->whenLoaded('account')),

        ];
    }
}
