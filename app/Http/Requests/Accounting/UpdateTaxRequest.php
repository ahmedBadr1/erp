<?php

namespace App\Http\Requests\Accounting;

use App\Models\Accounting\Account;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaxRequest extends FormRequest
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
            'code' => 'nullable|string',
            'rate' => 'nullable|string',
            'scope' => 'nullable|array',
            'account_id' => 'nullable|exists:accounts,id',
            'exclusive' => 'nullable|boolean',
            'active' => 'nullable|boolean',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->account_id) {
            $this->merge(['account_id' => Account::where('code', $this->account_id)->value('id')]);
        }
    }
}
