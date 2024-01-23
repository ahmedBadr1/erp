<?php

namespace App\Http\Requests\Accounting;

use Illuminate\Foundation\Http\FormRequest;

class DuplicateRequest extends FormRequest
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
            'type' => ['required','in:account,node,costCenterNode'],
            'account'  => 'required_if:type,account|exists:accounts,code',
            'node'  => 'required_if:type,node|exists:nodes,code',
            'costCenterNode'  => 'required_if:type,costCenterNode|exists:cost_center_nodes,code',


        ];
    }
}
