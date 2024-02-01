<?php

namespace App\Http\Requests\Accounting;

use App\Models\Accounting\Account;
use App\Models\Accounting\CostCenter;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
            'type' => 'required|in:CI,CO,JE',
            'treasury' => 'required_if:type,CI,CO|exists:accounts,id',
            'due' => 'nullable|date',
            'currency_id' => 'required|exists:currencies,id',
            'responsible' => 'required|exists:users,id',
            'paper_ref' => 'nullable|string',
            'accounts' => 'required|array',
            'accounts.*.id' => ['nullable', 'exists:accounts,id',],
            'accounts.*.cost_center_id' => ['nullable', 'exists:cost_centers,id',],
            'accounts.*.amount' => ['required_if:type,CI,CO', 'numeric', 'gt:0'],
            'accounts.*.c_amount' => ['required_if:type,JE', 'numeric', 'min:0'],
            'accounts.*.d_amount' => ['required_if:type,JE', 'numeric', 'min:0'],
            'accounts.*.comment' => ['nullable', 'string'],
            'amount' => 'required|numeric|gt:0',
            'note' => 'nullable|string',
        ];
    }

    public function prepareForValidation()
    {
        if ($this->accounts) {
            $accounts = [] ;
            foreach ($this->accounts as $i => $account) {
                $accounts[] = [
                    'id'=> Account::where('code', $account['code'])->value('id'),
                    'cost_center_id' =>CostCenter::where('code', $account['costCenter']['code'])->value('id'),
                    ...$account,
                ];
            }
            $this->merge([
                'accounts' => $accounts ,
            ]);
        }

        if ($this->treasury) {
            $this->merge([
                'treasury' => Account::where('code', $this->treasury)->value('id')
            ]);
        }
    }
}

