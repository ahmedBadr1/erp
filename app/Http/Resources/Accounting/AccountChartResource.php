<?php

namespace App\Http\Resources\Accounting;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountChartResource extends JsonResource
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
            'node_id' => $this->node_id,
            'credit' => $this->credit,
            'system' => $this->system,
            'active' => $this->active,
        ];
    }
}
