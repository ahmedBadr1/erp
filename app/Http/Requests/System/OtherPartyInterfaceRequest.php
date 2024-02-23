<?php

namespace App\Http\Requests\System;

use App\Models\Accounting\Account;
use Illuminate\Foundation\Http\FormRequest;

class OtherPartyInterfaceRequest extends FormRequest
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
            'other_party_id' => ['required','exists:other_parties,id'],
            'account_id' => ['nullable','exists:accounts,id'],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
           'account_id' => Account::where('code',$this->account_code)->value('id')
        ]);
    }

}
