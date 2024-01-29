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
//            'part_number'  => 'required|string|unique:products,part_number,' .$id ,
//            'sku'  => 'required|string|unique:products,sku,' .$id ,
            'part_number'  => 'nullable|string',
            'sku'  => 'nullable|string',


            'origin_number'  => 'nullable|string',
            'location'  => 'nullable|string',
            'oe_number'  => 'nullable|string',

            's_price'  =>'required|numeric|gt:0',
            'd_price'  => 'nullable|numeric|gt:0',
            'sd_price'  => 'nullable|numeric|gt:0',
            'min_price'  => 'nullable|numeric|gt:0',
            'ref_price'  => 'nullable|numeric|gt:0',
            'last_cost'  => 'nullable|numeric|gt:0',
            'avg_cost'  => 'required|string',
            'fifo'  =>  'nullable|numeric|min:0',
            'lifo'  =>  'nullable|numeric|min:0',
            'profit_margin'  =>  'nullable|numeric|min:0',
            'warranty'  => 'nullable|string',
            'valid_to'  => 'nullable|string',
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
            'product_category_id'  => 'nullable|exists:product_categories,id',
            'warehouse_id'  => 'nullable|exists:warehouses,id',
            'taxes'  => 'nullable|array',
            'taxes.*'  => 'nullable|exists:taxes,id',
            'unit_id' => 'nullable|numeric|exists:units,id',
            'unit_group_id' => 'nullable|numeric|exists:unit_groups,id',
            'brand_id'  => 'nullable|numeric|exists:brands,id',
            'suppliers.*'  =>'nullable|exists:accounts,id',
            'user_id'  => 'nullable|numeric|exists:users,id',
            'tags'  => 'nullable|array',
            'tags.*' => 'nullable|string',


        ];
    }

    public function prepareForValidation()
    {
//        if ($this->id){
//           $this->remove('code');
//        }
    }
}
