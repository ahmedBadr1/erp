<?php

namespace App\Http\Requests;

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
           'due' => 'nullable|date',
            'description' =>  'required|string',
            'entries' => 'required|array',
            'entries.*.account_id' => ['required', 'exists:accounts,id',],
            'entries.*.amount' => ['required', 'numeric', 'gt:0'],
            'entries.*.credit' => ['required', 'boolean']
        ];
    }
}
