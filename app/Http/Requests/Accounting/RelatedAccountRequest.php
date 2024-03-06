<?php

namespace App\Http\Requests\Accounting;

use App\Models\Accounting\Account;
use App\Models\Accounting\Node;
use Illuminate\Foundation\Http\FormRequest;

class RelatedAccountRequest extends FormRequest
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
            'account' => 'required|exists:accounts,id',
            'accounts.*' => 'required|exists:accounts,id',

        ];
    }
    public function prepareForValidation()
    {
        if ($this->account) {
            $this->merge(['account' => Account::where('code', $this->account)->value('id')]);
        }

        if ($this->accounts) {
            $this->merge([
                'accounts' => Account::whereIn('code', $this->accounts)->pluck('id')->toArray()
            ]);
        }
    }
}
