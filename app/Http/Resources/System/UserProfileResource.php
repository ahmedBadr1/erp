<?php

namespace App\Http\Resources\System;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $image = $this->image ? asset('storage/'.$this->image) : null ;
        return [
            'bio' => $this->profile->bio,
            'first_name' => $this->name['first'],
            'last_name' => $this->name['last'],
            'phone' => $this->phone,
            'image' => $image,
            'url' =>  $this->profile->url,
//            'first_name' => $this->first_name,
//            'last_name' => $this->last_name,
        ];
    }
}
