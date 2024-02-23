<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WarehouseInterfaceRequest extends FormRequest
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
            'warehouse_id' => ['required','exists:warehouses,id'],
            'cost_center_id' => ['nullable','exists:cost_centers,id'],
            'account_id' => ['nullable','exists:accounts,id'],

            'p_account_id' => ['nullable','exists:accounts,id'],
            'pr_account_id' => ['nullable','exists:accounts,id'],
            'pd_account_id' => ['nullable','exists:accounts,id'],

            's_account_id' => ['nullable','exists:accounts,id'],
            'sr_account_id' => ['nullable','exists:accounts,id'],
            'sd_account_id' => ['nullable','exists:accounts,id'],
            'ss_account_id' => ['nullable','exists:accounts,id'],
            'cog_account_id' => ['nullable','exists:accounts,id'],
            'or_account_id' => ['nullable','exists:accounts,id'],
        ];
    }

}
