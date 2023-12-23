<?php

namespace App\Http\Resources\Accounting;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'type' => $this->type,
            'amount' => $this->amount,
            'description' => $this->description,
            'post' => $this->post,
            'locked' => $this->locked,
            'due' => $this->due->format('d/m/Y'),
            'user' => new UserResource ($this->whenLoaded('user')),
            'entries' => EntryResource::collection($this->whenLoaded('entries')),
            'accounts' => AccountResource::collection($this->whenLoaded('accounts')),
        ];
    }
}
