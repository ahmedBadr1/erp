<?php

namespace App\Http\Requests\Accounting\Reports;

use App\Models\Accounting\Account;
use App\Models\Accounting\CostCenter;
use App\Models\Accounting\Node;
use Illuminate\Foundation\Http\FormRequest;

class ShowPostingRequest extends FormRequest
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
//            'codes.*' => 'nullable|exists:transactions,code',
            'transactionTypes' => 'nullable|array',
            'transactionTypes.*' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'accounts' => 'nullable|array',
            'accounts.*' => 'nullable|exists:accounts,id',
            'sellers' => 'nullable|array',
            'sellers.*' => 'nullable|exists:users,id',
            'posted' => 'nullable|boolean',
            'credit' => 'nullable|boolean',
            'debit' => 'nullable|boolean',

            'columns' => 'required|array',
            'columns.accounting_doc' => 'nullable|boolean',
            'columns.code' => 'nullable|boolean',
            'columns.date' => 'nullable|boolean',
            'columns.document_value' => 'nullable|boolean',
            'columns.note' => 'nullable|boolean',
            'columns.paper_ref' => 'nullable|boolean',
            'columns.second_party' => 'nullable|boolean',
            'columns.un_post_date' => 'nullable|boolean',
            'columns.un_post_user' => 'nullable|boolean',
            'columns.wh_doc' => 'nullable|boolean',
        ];
    }

    public function prepareForValidation()
    {
        if ($this->accounts) {
            $this->merge([
                'accounts' => Account::whereIn('code', $this->accounts)->pluck('id')->toArray()
            ]);
        }

    }
}
