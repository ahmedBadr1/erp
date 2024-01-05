<?php

namespace App\Http\Resources\Purchases;

use App\Http\Resources\Hr\EmployeesResource;
use App\Http\Resources\System\LocationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SuppliersResource extends JsonResource
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
            'name'=> $this->name  ,
            'phone' => $this->phone,
            'code' => $this->code ,
            'email' => $this->email,
//            'locations' =>  new LocationResource($this->whenLoaded('locations')),
//            'business_name', 'name', 'code', 'responsible_id', 'credit_limit', 'tax_number',
//            'registration_number', 'payment_method', 'phone', 'telephone', 'email', 'active'
//
           'employee' => new EmployeesResource($this->whenLoaded('employee')),
//            'comments' => CommentResource::collection($this->whenLoaded('comments')),

        ];
    }
}
