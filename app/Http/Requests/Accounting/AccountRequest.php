<?php

namespace App\Http\Requests\Accounting;

use Illuminate\Foundation\Http\FormRequest;

class AccountRequest extends FormRequest
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
            'name' => 'required|string',
            'description' => 'nullable|string',
            'node_id' => 'required|exists:nodes,id',
            'currency_id' => 'required|exists:currencies,id',
            'opening_balance' => 'nullable|numeric|gt:0',
            'opening_balance_date' => 'nullable|date',
            'active' => 'required|boolean',
        ];
    }
}
