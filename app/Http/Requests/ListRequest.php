<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'keywords' => "string|nullable",
            'orderBy'=> "string|nullable",
            'orderDesc' => "nullable|string|in:asc,desc",
            'per_page' => "numeric|nullable",
            'start_date' => "nullable|date",
            'end_date' => "nullable|date",
            'current_page'=> "numeric|nullable",
            'export' => "nullable|string|in:csv,excel,pdf",
        ];
    }
}
