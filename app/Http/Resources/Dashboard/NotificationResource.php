<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class NotificationResource extends JsonResource
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
            'from' => $this->data['from'] ?? null,
            'title' => $this->data['title'] ?? null,
            'message' => Str::limit($this->data['message'],30) ?? null,
            'url' => $this->data['url'] ?? null,
            'created_at' => $this->created_at->diffForHumans()

        ];
    }
}
