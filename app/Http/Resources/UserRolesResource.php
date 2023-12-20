<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserRolesResource extends JsonResource
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
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'lang' => $this->lang,
            'image' => $this->image,
            'active' => $this->active,
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            "role" => $this->getRoleNames()[0],
            "permissions" => $this->getPermissionsViaRoles()->pluck("name"),
        ];
    }
}
