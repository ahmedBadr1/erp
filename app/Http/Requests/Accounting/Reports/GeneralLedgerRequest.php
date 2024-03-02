<?php

namespace App\Http\Requests\Accounting\Reports;

use App\Models\Accounting\Account;
use App\Models\Accounting\CostCenter;
use App\Models\Accounting\CostCenterNode;
use App\Models\Accounting\Node;
use Illuminate\Foundation\Http\FormRequest;

class GeneralLedgerRequest extends FormRequest
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
            'nodeLevel' => 'nullable',
            'costCenterNode' => 'nullable|array',
            'costCenterNode.*' => 'nullable|exists:cost_center_nodes,id',
            'costCenters' => 'nullable|array',
            'costCenters.*' => 'nullable|exists:cost_centers,id',
            'dateType' => 'required|in:period,trail',
            'from_date' => 'required_if:dateType,period|date',
            'to_date' => 'required_if:dateType,period|date',
            'date' => 'required_if:dateType,trail|date',

            'posting' => 'nullable|string|in:posted,unposted,all',
            'slow_accounts' => 'nullable|boolean',
            'with_transactions' => 'nullable|boolean',
            'replacement_orders' => 'nullable|boolean',
            'year_to_date' => 'nullable|boolean',
            'accounts_only' => 'nullable|boolean',
            'has_balance' => 'nullable|boolean',
            'actual_to_budget' => 'nullable|boolean',
            'cash_bases' => 'nullable|boolean',
            'other_currency' => 'nullable|boolean',

            'columns' => 'required|array',
            'columns.code' => 'nullable|boolean',
            'columns.net_credit' => 'nullable|boolean',
            'columns.net_debit' => 'nullable|boolean',
            'columns.total_credit' => 'nullable|boolean',
            'columns.total_debit' => 'nullable|boolean',
            'columns.opening_credit' => 'nullable|boolean',
            'columns.opening_debit' => 'nullable|boolean',
            'columns.account_owner_databases' => 'nullable|boolean',
            'columns.related_net_credit' => 'nullable|boolean',
            'columns.related_net_debit' => 'nullable|boolean',
            'columns.related_accounts_names' => 'nullable|boolean',
            'columns.typical_balance' => 'nullable|boolean',
            'columns.client_code' => 'nullable|boolean',

        ];
    }

    public function prepareForValidation()
    {
        if ($this->costCenterNode) {
            $this->merge([
                'costCenterNode' => costCenterNode::where('code', $this->node)->pluck('id')->toArray()
            ]);
        }


        if ($this->costCenters) {
            $this->merge([
                'costCenters' => CostCenter::whereIn('code', $this->costCenters)->pluck('id')->toArray()
            ]);
        }

    }
}
