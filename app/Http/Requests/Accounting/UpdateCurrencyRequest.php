<?php

namespace App\Http\Requests\Accounting;

use App\Models\Accounting\Account;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCurrencyRequest extends FormRequest
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
            'name' => 'nullable|string',
            'symbol' => 'nullable|string',
            'ex_rate' => 'nullable|numeric|min:0',
            'gain_account' => 'nullable|exists:accounts,id',
            'loss_account' => 'nullable|exists:accounts,id',
            'sub_unit' => 'nullable|string',
            'active' => 'nullable|boolean',
        ];
    }

    protected function prepareForValidation()
    {
        $gain_account = Account::where('code', $this->gain_account)->value('id');
        if ($gain_account) {
            $this->merge(['gain_account' => $gain_account]);
        }

        $loss_account = Account::where('code', $this->loss_account)->value('id');
        if ($loss_account) {
            $this->merge(['loss_account' => $loss_account]);
        }
    }
}
