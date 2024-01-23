<?php

namespace App\Http\Requests\Accounting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountRequest extends FormRequest
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
            'credit_limit' => 'nullable|numeric|min:0',
            'debit_limit' => 'nullable|numeric|min:0',
//            'node_id' => 'required|exists:nodes,id',
//            'currency_id' => 'required|exists:currencies,id',
            'active' => 'nullable|boolean',

            'groups.*' => 'nullable|exists:groups,id',
            'users.*' => 'nullable|exists:users,id',
            'tags.*' => 'nullable|string',

            'contact' => 'nullable|array',
            'contact.name' => 'nullable|string',
            'contact.phone1' => 'nullable|string',
            'contact.phone2' => 'nullable|string',
            'contact.whatsapp' => 'nullable|string',
            'contact.fax' => 'nullable|string',
            'contact.email' => 'nullable|string',

            'address' => 'nullable|array',
            'address.district' => 'nullable|string',
            'address.street' => 'nullable|string',
            'address.postal_code' => 'nullable|string',
            'address.building' => 'nullable|string',
            'address.apartment' => 'nullable|string',
            'address.city_id' => 'nullable|string',
        ];
    }
}
