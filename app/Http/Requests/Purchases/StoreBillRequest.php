<?php

namespace App\Http\Requests\Purchases;

use Illuminate\Foundation\Http\FormRequest;

class StoreBillRequest extends FormRequest
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

            'type' => 'required|string|in:PO',
            'warehouse_id' => 'required|exists:warehouses,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'responsible_id' => 'required|exists:users,id',
            'treasury_id' => 'required|exists:accounts,id',

            'date' => 'required|date',
            'deliver_at' => 'nullable|date',// |after:date
            'currency_id' => 'required|exists:currencies,id',
            'paper_ref' => 'nullable|string',

            'items' => 'required|array',
            'items.*.product_id' => ['required', 'exists:products,id',],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.price' => ['required', 'numeric', 'gt:0'],
            'items.*.total' => ['required', 'numeric', 'gt:0'],
            'items.*.barcode' => ['nullable', 'string'],
            'items.*.comment' => ['nullable', 'string'],

            'gross_total' => 'required|numeric|gt:0',
            'discount' => 'required|numeric|min:0',
            'tax_total' => 'nullable|numeric|min:0',
            'sub_total' => 'required|numeric|gt:0',
            'total' => 'required|numeric|gt:0',

            'note' => 'nullable|string',



            // {
            //    "type": "PO",
            //    "due": "2024-01-31 18:39:25 +02:00Z",
            //    "deliver_at": "2024-01-31 18:39:25 +02:00Z",
            //    "currency_id": "1",
            //    "warehouse": {
            //        "name": "مخزن 1",
            //        "id": 1
            //    },
            //    "supplier": 1,
            //    "responsible": 1,
            //    "paper_ref": "",
            //    "items": [
            //        {
            //            "name": "شارع آثار السباعي",
            //            "product_id": 15,
            //            "quantity": "10",
            //            "price": "30",
            //            "total": 300,
            //            "comment": "2135"
            //        }
            //    ],
            //    "gross_total": "300.00",
            //    "discount": 0,
            //    "sub_total": "300.00",
            //    "total": "300.00",
            //    "note": ""
            //}
        ];
    }
}
