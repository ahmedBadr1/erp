<?php

namespace App\Http\Resources\Inventory;

use App\Enums\Inventory\UnitTypesEnum;
use App\Http\Resources\NameResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
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
            'code' => $this->whenNotNull($this->code),
            'order' => $this->whenNotNull($this->order),
//            'type' => $this->whenNotNull(UnitTypesEnum::get($this->type)),
            'type' => $this->whenNotNull($this->type),

            'ratio' => $this->whenNotNull($this->ratio),
            'products_count' => $this->whenCounted('products'),
        ];
    }
}
