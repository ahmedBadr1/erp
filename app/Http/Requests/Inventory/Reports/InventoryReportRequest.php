<?php

namespace App\Http\Requests\Inventory\Reports;

use Illuminate\Foundation\Http\FormRequest;

class InventoryReportRequest extends FormRequest
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
            'orderTypes' => "nullable|boolean",
            'warehouses' => "nullable|boolean",
            'products' => "nullable|boolean",
            'buyers' => "nullable|boolean",
            'sellers' => "nullable|boolean",
            'clients' => "nullable|boolean",
            'suppliers' => "nullable|boolean",
            'tags' => "nullable|boolean",
            'users' => "nullable|boolean",
            'warehouseTypes' => "nullable|boolean",
            'brands' => "nullable|boolean",
            'branches' => "nullable|boolean",
            'productCategories' => "nullable|boolean",
            'inOther' => "nullable|boolean",
            'outOther' => "nullable|boolean",




        ];
    }
}
