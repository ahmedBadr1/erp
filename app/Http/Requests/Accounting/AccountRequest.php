<?php

namespace App\Http\Requests\Accounting;

use App\Models\Accounting\Node;
use Illuminate\Foundation\Http\FormRequest;

class AccountRequest extends FormRequest
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
            'node_id' => 'required|exists:nodes,id',
            'currency_id' => 'required|exists:currencies,id',

            'groups.*' => 'nullable|exists:groups,id',
            'users.*' => 'nullable|exists:users,id',
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
            'address.postal_code' => 'nullable|string',
            'address.building' => 'nullable|string',
            'address.apartment' => 'nullable|string',
            'address.city_id' => 'nullable|string',

//            'opening_balance' => 'nullable|numeric|gt:0',
//            'opening_date' => 'nullable|date',
//            'active' => 'required|boolean',
        ];
    }
    public function prepareForValidation()
    {
        $this->merge([
            'node_id' => Node::where('code',$this->node)->value('id')
        ]);
    }
}
