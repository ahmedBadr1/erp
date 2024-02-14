<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'space' => 'nullable|string',
            'height' => 'nullable|string',

//            'manager_id' => 'required|exists:users,id',
            'client_id' => 'nullable|exists:clients,id',
            'price_list_id' => 'nullable|exists:price_lists,id',
            'shelves.*' => 'nullable|exists:warehouse_shelves,id',

            'is_rma' => 'nullable|boolean',
            'is_rented' => 'nullable|boolean',
            'has_security' => 'nullable|boolean',

            'tags.*' => 'nullable|string',


            'contact' => 'required|array',
            'contact.name' => 'nullable|string',
            'contact.phone1' => 'nullable|string',
            'contact.phone2' => 'nullable|string',
            'contact.whatsapp' => 'nullable|string',
            'contact.fax' => 'nullable|string',
            'contact.email' => 'nullable|string',

            'address' => 'required|array',
            'address.district' => 'nullable|string',
            'address.street' => 'nullable|string',
//            'address.postal_code' => 'nullable|string',
            'address.building' => 'nullable|string',
            'address.apartment' => 'nullable|string',
            'address.city_id' => 'nullable|string',


            'contract.start_date' => 'nullable|date',
            'contract.end_date' => 'nullable|date',
        ];
    }
}
