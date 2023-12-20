<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'from' => $this->data['from'],
            'title' => $this->data['title'],
            'message' => $this->data['message'],
            'url' => $this->data['url'],
            'created_at' => $this->created_at->diffForHumans()

        ];
    }
}
