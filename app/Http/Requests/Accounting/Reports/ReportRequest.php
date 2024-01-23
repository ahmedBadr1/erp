<?php

namespace App\Http\Requests\Accounting\Reports;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
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
            'accounts' => "nullable|boolean",
            'nodes' => "nullable|boolean",
            'currencies' => "nullable|boolean",
            'clients' => "nullable|boolean",
            'costCenters' => "nullable|boolean",
            'sellers' => "nullable|boolean",
            'taxes' => "nullable|boolean",
            'treasuries' => "nullable|boolean",
            'tree'=> "nullable|boolean",
            'accountTypes'=> "nullable|boolean",
            'nodeRoots' => "nullable|boolean",


        ];
    }
}
