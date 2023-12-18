<?php

namespace App\Http\Resources\Hr;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeesResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'salary' => $this->salary,
            'address' => $this->address,
            'joined_at' => $this->joined_at,
            'joined' => $this->joined_at?->diffForHumans(),
            'overtime' => $this->overtime,
            'active' => $this->active,
            'updated_at' => $this->updated_at->diffForHumans(),
            'removed' => $this->removed,
            'joptitle_id' => $this->joptitle_id,
            'joptitle' => $this->joptitle?->name,
            'department' => $this->department?->name,
            'department_id' => $this->department_id,
            'status_id' => $this->status_id,
            'status' => $this->status?->name,
        ];   
    }
}
