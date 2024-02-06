<?php

namespace App\Http\Requests\Inventory\Reports;

use Illuminate\Foundation\Http\FormRequest;

class StockCardsReportRequest extends FormRequest
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
            'warehouse' => 'nullable|exists:warehouses,id',
            'product' => 'nullable|exists:products,id',

            'orderTypes' => 'nullable|array',
            'orderTypes.*' => 'nullable|string',
            'sellers' => 'nullable|array',
            'sellers.*' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'suppliers' => 'nullable|array',
            'suppliers.*' => 'nullable|exists:suppliers,id',
            'clients' => 'nullable|array',
            'clients.*' => 'nullable|exists:clients,id',
            'show_supplier' => 'required|boolean',
            'show_client' => 'required|boolean',
            'in_other'=> 'nullable',
            'out_other'=> 'nullable',


            'columns' => 'required|array',
            'columns.second_party' => 'nullable|boolean',
            'columns.date' => 'nullable|boolean',
            'columns.quantity_in' => 'nullable|boolean',
            'columns.quantity_out' => 'nullable|boolean',
            'columns.local_balance' => 'nullable|boolean',
            'columns.work_order_type' => 'nullable|boolean',
            'columns.unit_of_measure' => 'nullable|boolean',
            'columns.batch_number' => 'nullable|boolean',
            'columns.production_date' => 'nullable|boolean',
            'columns.expire_date' => 'nullable|boolean',
            'columns.paper_ref' => 'nullable|boolean',

            'columns.items_serials' => 'nullable|boolean',
            'columns.created_at' => 'nullable|boolean',
            'columns.created_by' => 'nullable|boolean',
            'columns.edited_at' => 'nullable|boolean',
            'columns.edited_by' => 'nullable|boolean',

        ];
    }

    public function prepareForValidation()
    {
        //
    }
}
