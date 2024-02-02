<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvTransactionRequest extends FormRequest
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

            'type' => 'required|string|in:RS,IO,IR,RR,IT,RT',
            'warehouse_id' => 'required|exists:warehouses,id',
            'supplier' => "required|boolean",
            'supplier_id' => 'required_if:supplier,1|exists:suppliers,id',
            'other_id' => 'required_if:supplier,0',
            'responsible_id' => 'required|exists:users,id',

            'date' => 'required|date',
            'deliver_at' => 'nullable|date',// |after:date
            'paper_ref' => 'nullable|string',

            'items' => 'required|array',
            'items.*.product_id' => ['required', 'exists:products,id',],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.price' => ['required', 'numeric', 'gt:0'],
            'items.*.total' => ['required', 'numeric', 'gt:0'],
            'items.*.barcode' => ['nullable', 'string'],
            'items.*.comment' => ['nullable', 'string'],
            'total' => 'required|numeric|gt:0',
            'note' => 'nullable|string',


        ];
    }
}
