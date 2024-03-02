<?php

namespace App\Http\Resources\Accounting;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NodeResource extends JsonResource
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
            'slug' => $this->whenNotNull($this->slug),
            'credit' => $this->whenNotNull($this->credit),
            'account_type_id' => $this->whenNotNull($this->account_type_id),
            'parent_id' => $this->whenNotNull($this->parent_id),
            'active' => $this->whenNotNull($this->active),
            'usable' => $this->whenNotNull($this->usable),
            'system' => $this->whenNotNull($this->system),
            'children_count' => $this->whenCounted('children'),
            'children' => self::collection($this->whenLoaded('children')),
            'accounts' => AccountChartResource::collection($this->whenLoaded('accounts')),
        ];
    }
}
