<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NameResource extends JsonResource
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
            'name' =>  $this->whenNotNull($this->name),
            'username' =>  $this->whenNotNull($this->username),
            'code' => $this->whenNotNull($this->code),
            'tax_id' => $this->whenNotNull($this->tax_id),
        ];
    }
}
