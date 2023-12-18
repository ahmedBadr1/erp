<?php

namespace App\Http\Resources\Purchases;

use App\Http\Resources\Hr\EmployeesResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowVendorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $location =  $this->whenLoaded('locations')?->first();
        $contacts =  $this->whenLoaded('contacts');

        $account =  $this->whenLoaded('account');

        return [
            'id' => $this->id,
            'business_name'=> $this->business_name  ,
            'name'=> $this->name  ,
            'phone' => $this->phone,
            'code' => $this->code ,
            'email' => $this->email,
            'address' => $location?->address,
            'responsible_id' => $this->responsible_id,
            'credit_limit' => $this->credit_limit,
            'warranty' => $this->warranty,
            'tax_number' => $this->tax_number,
            'registration_number' => $this->registration_number,
            'payment_method' => $this->payment_method,
            'account_name' => $account?->name,
            'opening_balance' => $account?->opening_balance,
            'opening_balance_date' => $account?->opening_balance_date,
            'currency_id' => $account?->currency_id,
            'contacts' => $contacts
//            'locations' =>  new LocationResource($this->whenLoaded('locations')),
//            'business_name', 'name', 'code', 'responsible_id', 'credit_limit', 'tax_number',
//            'registration_number', 'payment_method', 'phone', 'telephone', 'email', 'active'
        ];
    }
}
