<?php

namespace App\Http\Requests\Inventory\Reports;

use Illuminate\Foundation\Http\FormRequest;

class WarehousesReportRequest extends FormRequest
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
            'warehouses' => 'nullable|array',
            'warehouses.*' => 'nullable|exists:warehouses,id',
            'products' => 'nullable|array',
            'products.*' => 'nullable|exists:products,id',
            'brands' => 'nullable|array',
            'brands.*' => 'nullable|exists:brands,id',
            'date' => 'nullable|date',
            'has_balance' => 'nullable',
            'pending' => 'required|boolean',
            'reserved' => 'required|boolean',
            'serials' => 'required|boolean',

            'columns' => 'required|array',
            'columns.name' => 'nullable|boolean',
            'columns.code' => 'nullable|boolean',
            'columns.part_number' => 'nullable|boolean',
            'columns.tags' => 'nullable|boolean',
            'columns.s_price' => 'nullable|boolean',
            'columns.d_price' => 'nullable|boolean',
            'columns.sd_price' => 'nullable|boolean',
            'columns.ref_price' => 'nullable|boolean',
            'columns.location' => 'nullable|boolean',
            'columns.warranty' => 'nullable|boolean',
            'columns.oe_number' => 'nullable|boolean',
            'columns.default_uom' => 'nullable|boolean',
            'columns.weight' => 'nullable|boolean',
            'columns.batch_number' => 'nullable|boolean',
            'columns.production_date' => 'nullable|boolean',
            'columns.expiration_date' => 'nullable|boolean',
            'columns.local_balance' => 'nullable|boolean',
            'columns.local_balance_detailed' => 'nullable|boolean',
            'columns.local_opening_balance' => 'nullable|boolean',
            'columns.local_opening_balance_detailed' => 'nullable|boolean',
            'columns.global_balance' => 'nullable|boolean',
            'columns.global_opening_balance' => 'nullable|boolean',
            'columns.global_opening_balance_detailed' => 'nullable|boolean',
            'columns.local_maximum_point' => 'nullable|boolean',
            'columns.local_minimum_point' => 'nullable|boolean',
            'columns.local_reordering_point' => 'nullable|boolean',
            'columns.global_maximum_point' => 'nullable|boolean',
            'columns.global_minimum_point' => 'nullable|boolean',
            'columns.global_reordering_point' => 'nullable|boolean',


        ];
    }

    public function prepareForValidation()
    {
        //
    }
}
