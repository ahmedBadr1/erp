<?php

namespace App\Http\Requests\Accounting;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionTypeRequest extends FormRequest
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
            'type' => 'required|in:ci,co',
            'credit_account' => 'required|exists:accounts,code',
            'debit_account' => 'required|exists:accounts,code',
            'amount' => 'required|numeric|gt:0',
            'cost_center' => 'nullable|exists:cost_centers,code',
            'description' => 'nullable|string',
            'due' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id',
        ];
    }
}
