<?php

namespace App\Http\Requests\Accounting\Reports;

use App\Models\Accounting\Account;
use App\Models\Accounting\CostCenter;
use App\Models\Accounting\Node;
use Illuminate\Foundation\Http\FormRequest;

class AccountLedgerRequest extends FormRequest
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
            'node' => 'nullable|exists:nodes,id',
            'accounts' => 'nullable|array',
            'accounts.*' => 'nullable|exists:accounts,id',
            'costCenters' => 'nullable|array',
            'costCenters.*' => 'nullable|exists:cost_centers,id',
            'currency' => 'nullable|exists:currencies,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',

            'clients' => 'nullable|array',
            'clients.*' => 'nullable|exists:clients,id',
            'sellers' => 'nullable|array',
            'sellers.*' => 'nullable|exists:users,id',
            'detailed' => 'nullable|boolean',
            'posting' => 'nullable|string|in:posted,unposted,all',
            'related_accounts' => 'nullable|boolean',
            'with_transactions' => 'nullable|boolean',

            'columns' => 'required|array',
            'columns.attachments' => 'nullable|boolean',
            'columns.balance' => 'nullable|boolean',
            'columns.client' => 'nullable|boolean',
            'columns.client_code' => 'nullable|boolean',
            'columns.comments' => 'nullable|boolean',
            'columns.cost_center' => 'nullable|boolean',
            'columns.created_at' => 'nullable|boolean',
            'columns.created_by' => 'nullable|boolean',
            'columns.credit' => 'nullable|boolean',
            'columns.debit' => 'nullable|boolean',
            'columns.date' => 'nullable|boolean',
            'columns.detailed_balance' => 'nullable|boolean',
            'columns.document_currency' => 'nullable|boolean',
            'columns.document_exchange_rate' => 'nullable|boolean',
            'columns.document_value' => 'nullable|boolean',
            'columns.edited_at' => 'nullable|boolean',
            'columns.edited_by' => 'nullable|boolean',
            'columns.ledger_code' => 'nullable|boolean',
            'columns.ledger_ref' => 'nullable|boolean',
            'columns.line_value' => 'nullable|boolean',
            'columns.movement_type' => 'nullable|boolean',
            'columns.note' => 'nullable|boolean',
            'columns.paper_ref' => 'nullable|boolean',
            'columns.original_document' => 'nullable|boolean',
            'columns.period_balance' => 'nullable|boolean',
            'columns.related_documents' => 'nullable|boolean',
            'columns.related_orders' => 'nullable|boolean',
            'columns.responsible' => 'nullable|boolean',
            'columns.second_party' => 'nullable|boolean',
            'columns.second_party_cost_center' => 'nullable|boolean',
            'columns.second_party_value' => 'nullable|boolean',
            'columns.supplier' => 'nullable|boolean',
            'columns.tax_document' => 'nullable|boolean',
            'columns.warehouse_document' => 'nullable|boolean',
        ];
    }

    public function prepareForValidation()
    {
        if ($this->node) {
            $this->merge([
                'node' => Node::where('code', $this->node)->pluck('id')->toArray()
            ]);
        }

        if ($this->accounts) {
            $this->merge([
                'accounts' => Account::whereIn('code', $this->accounts)->pluck('id')->toArray()
            ]);
        }

        if ($this->costCenters) {
            $this->merge([
                'costCenters' => CostCenter::whereIn('code', $this->costCenters)->pluck('id')->toArray()
            ]);
        }

    }
}
