<?php

namespace App\Http\Requests\Inventory\Reports;

use App\Models\Accounting\Account;
use App\Models\Accounting\CostCenter;
use App\Models\Accounting\Node;
use Illuminate\Foundation\Http\FormRequest;

class WorkOrdersReportRequest extends FormRequest
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
            'codes' => 'nullable|array',
//            'codes.*' => 'nullable|exists:inv_transactions,code',
            'orderTypes' => 'nullable|array',
            'orderTypes.*' => 'nullable|string',
            'sellers' => 'nullable|array',
            'sellers.*' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'warehouses' => 'nullable|array',
            'warehouses.*' => 'nullable|exists:warehouses,id',
            'suppliers' => 'nullable|array',
            'suppliers.*' => 'nullable|exists:suppliers,id',
            'clients' => 'nullable|array',
            'clients.*' => 'nullable|exists:clients,id',
            'users' => 'nullable|array',
            'users.*' => 'nullable|exists:users,id',
            'external' => 'required|boolean',
            'show_supplier' => 'required|boolean',
            'show_client' => 'required|boolean',

            'columns' => 'required|array',
            'columns.code' => 'nullable|boolean',
            'columns.total_value' => 'nullable|boolean',
            'columns.date' => 'nullable|boolean',
            'columns.second_party' => 'nullable|boolean',
            'columns.related_order' => 'nullable|boolean',
            'columns.currency' => 'nullable|boolean',
            'columns.second_party_mobile' => 'nullable|boolean',
            'columns.type' => 'nullable|boolean',
            'columns.warehouse' => 'nullable|boolean',
            'columns.paper_ref' => 'nullable|boolean',
            'columns.status' => 'nullable|boolean',
            'columns.created_at' => 'nullable|boolean',
            'columns.created_by' => 'nullable|boolean',
            'columns.edited_at' => 'nullable|boolean',
            'columns.edited_by' => 'nullable|boolean',
            'columns.ex_rate' => 'nullable|boolean',
            'columns.note' => 'nullable|boolean',

        ];
    }

    public function prepareForValidation()
    {
        //
    }
}
