<?php

namespace App\Http\Requests\Accounting;

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
            'treasury' => 'required_if:type,CI,CO|exists:accounts,code',
            'je_code' => 'nullable|string',// exists:ledger,code
            'due' => 'nullable|date',
            'currency' => 'required|exists:currencies,id',
            'responsible' => 'required|exists:users,id',
            'document_no' => 'nullable|string',
            'accounts' => 'required|array',
            'accounts.*.code' => ['required', 'exists:accounts,code',],
            'accounts.*.costCenter.code' => ['nullable', 'exists:cost_centers,code',],
            'accounts.*.amount' => ['required_if:type,CI,CO', 'numeric', 'gt:0'],
            'accounts.*.c_amount' => ['required_if:type,JE', 'numeric', 'min:0'],
            'accounts.*.d_amount' => ['required_if:type,JE', 'numeric', 'min:0'],
            'accounts.*.comment' => ['nullable', 'string'],
            'amount' => 'required|numeric|gt:0',
            'description' => 'nullable|string',
        ];
    }
}

