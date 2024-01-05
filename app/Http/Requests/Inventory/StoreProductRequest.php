<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
        $id = $this->id ?? 'NULL';
        return [
            'name' => 'required|string',
            'short_name'  => 'nullable|string',
            'code'  => 'required|string|unique:products,code,' .$id ,
            'origin_number'  => 'nullable|string',
            'type'  => 'nullable|string',
            'price'  =>'required|numeric|gt:0',
            'd_price'  => 'nullable|numeric|gt:0',
            'sd_price'  => 'nullable|numeric|gt:0',
            'min_price'  => 'nullable|numeric|gt:0',
            'ref_price'  => 'nullable|numeric|gt:0',
//            'last_cost'  => 'nullable|numeric|gt:0',
//            'avg_cost'  => 'required|string',
            'first_cost'  =>  'nullable|numeric|min:0',
            'profit_margin'  =>  'nullable|numeric|min:0',
            'warranty'  => 'nullable|string',
            'expire_date'  => 'nullable|string',
            'barcode'  => 'nullable|string',
            'hs_code'  => 'nullable|string',
            'batch_number'  => 'nullable|string',
            'weight'  =>  'nullable|numeric|min:0',
            'width'  =>  'nullable|numeric|min:0',
            'length'  =>  'nullable|numeric|min:0',
            'height'   =>  'nullable|numeric|min:0',
            'max_limit'  => 'nullable|numeric|min:0',
            'min_limit'  =>  'nullable|numeric|gt:0',
            'require_barcode'  => 'nullable|boolean',
            'repeat_barcode' => 'nullable|boolean',
            'negative_stock' => 'required|boolean',
            'can_be_sold' => 'required|boolean',
            'can_be_purchased' => 'required|boolean',
            'returnable' => 'required|boolean',
            'active' => 'required|boolean',
            'inv_category_id'  => 'nullable|exists:inv_categories,id',
            'warehouse_id'  => 'nullable|exists:warehouses,id',
            'taxes'  => 'nullable|array',
            'taxes.*'  => 'nullable|exists:taxes,id',
            'unit_id' => 'required|numeric|exists:units,id',
            'brand_id'  => 'nullable|numeric|exists:brands,id',
            'supplier_id'  => 'nullable|numeric|exists:suppliers,id',
            'user_id'  => 'nullable|numeric|exists:users,id',
        ];
    }

    public function prepareForValidation()
    {
//        if ($this->id){
//           $this->remove('code');
//        }
    }
}
