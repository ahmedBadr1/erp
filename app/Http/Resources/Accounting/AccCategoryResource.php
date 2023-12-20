<?php

namespace App\Http\Resources\Accounting;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccCategoryResource extends JsonResource
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
            'slug' => $this->slug,
            'credit' => $this->credit,
            'parent_id' => $this->parent_id,
            'active' => $this->active,
            'usable' => $this->usable,
            'system' => $this->system,
            'children_count' => $this->whenCounted('children'),
            'children' => self::collection($this->whenLoaded('children')),
            'accounts' => AccountChartResource::collection($this->whenLoaded('accounts')),
        ];
    }
}
