<?php

namespace App\Http\Requests\Accounting;

use App\Models\Accounting\Account;
use Illuminate\Foundation\Http\FormRequest;

class CreateTaxRequest extends FormRequest
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
            'code' => 'required|string',
            'rate' => 'required|string',
            'scope' => 'required|array',
            'account' => 'nullable|exists:accounts,id',
            'exclusive' => 'required|boolean',
            'active' => 'required|boolean',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->account) {
            $this->merge(['account' => Account::where('code', $this->account)->value('id')]);
        }
    }
}
