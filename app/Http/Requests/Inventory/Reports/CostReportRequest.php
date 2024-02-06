<?php

namespace App\Http\Requests\Inventory\Reports;

use Illuminate\Foundation\Http\FormRequest;

class CostReportRequest extends FormRequest
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
            'columns.category' => 'nullable|boolean',
            'columns.name' => 'nullable|boolean',
            'columns.part_number' => 'nullable|boolean',
            'columns.local_balance' => 'nullable|boolean',
            'columns.reserved' => 'nullable|boolean',
            'columns.code' => 'nullable|boolean',
            'columns.brand' => 'nullable|boolean',
            'columns.s_price' => 'nullable|boolean',
            'columns.d_price' => 'nullable|boolean',
            'columns.sd_price' => 'nullable|boolean',
            'columns.global_balance' => 'nullable|boolean',

            'columns.fifo' => 'nullable|boolean',
            'columns.lifo' => 'nullable|boolean',
            'columns.avg_cost' => 'nullable|boolean',
            'columns.last_cost' => 'nullable|boolean',
            'columns.lowest_cost' => 'nullable|boolean',
            'columns.last_po_price' => 'nullable|boolean',
            'columns.last_po_currency' => 'nullable|boolean',
            'columns.expected_receive' => 'nullable|boolean',
            'columns.last_356_day_profit' => 'nullable|boolean',

        ];
    }

    public function prepareForValidation()
    {
        //
    }
}
