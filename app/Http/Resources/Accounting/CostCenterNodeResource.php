<?php

namespace App\Http\Resources\Accounting;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CostCenterNodeResource extends JsonResource
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
            'parent_id' => $this->parent_id,
            'active' => $this->active,
            'children_count' => $this->whenCounted('children'),
            'children' => self::collection($this->whenLoaded('children')),
            'costCenters' => CostCenterResource::collection($this->whenLoaded('costCenters')),
        ];
    }
}
