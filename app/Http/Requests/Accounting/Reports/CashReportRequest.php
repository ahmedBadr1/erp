<?php

namespace App\Http\Requests\Accounting\Reports;

use App\Models\Accounting\Account;
use App\Models\Accounting\CostCenter;
use App\Models\Accounting\Node;
use Illuminate\Foundation\Http\FormRequest;

class CashReportRequest extends FormRequest
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
            'cashin' => 'nullable|boolean',
            'cashout' => 'nullable|boolean',
            'code' => 'nullable|exists:transactions,code',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'partners' => 'nullable|array',
            'partners.*' => 'nullable|exists:accounts,id',
            'treasuries' => 'nullable|array',
            'treasuries.*' => 'nullable|exists:accounts,id',
            'sellers' => 'nullable|array',
            'sellers.*' => 'nullable|exists:users,id',
            'detailed' => 'nullable|boolean',
            'posting' => 'nullable|string|in:posted,unposted,all',
            'related_clients' => 'nullable|boolean',
            'related_sales_docs' => 'nullable|boolean',

            'columns' => 'required|array',
            'columns.code' => 'nullable|boolean',
            'columns.date' => 'nullable|boolean',
            'columns.second_party' => 'nullable|boolean',
            'columns.edited_at' => 'nullable|boolean',
            'columns.edited_by' => 'nullable|boolean',
            'columns.document_value' => 'nullable|boolean',
            'columns.note' => 'nullable|boolean',
            'columns.paper_ref' => 'nullable|boolean',
            'columns.related_orders' => 'nullable|boolean',
            'columns.seller' => 'nullable|boolean',
            'columns.responsible' => 'nullable|boolean',
            'columns.warehouse' => 'nullable|boolean',


        ];
    }

    public function prepareForValidation()
    {


        if ($this->partners) {
            $this->merge([
                'partners' => Account::whereIn('code', $this->partners)->pluck('id')->toArray()
            ]);
        }

        if ($this->treasuries) {
            $this->merge([
                'treasuries' => Account::whereIn('type_code', $this->treasuries)->pluck('id')->toArray()
            ]);
        }

    }
}
