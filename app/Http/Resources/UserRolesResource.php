<?php

namespace App\Http\Resources;

use App\Http\Resources\System\BookmarkResource;
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
        $image = $this->image ? asset('storage/'.$this->image) : null ;
        return [
            'id' => $this->id,
            'name' => $this->fullName,
            'username' => $this->username,
            'email' => $this->email,
            'lang' => $this->lang,
            'image' => $image,
            'active' => $this->active,
            'bookmarks' => BookmarkResource::collection($this->whenLoaded('bookmarks')),
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            "role" => $this->getRoleNames()[0],
            "permissions" => $this->getPermissionsViaRoles()->pluck("name"),
        ];
    }
}
