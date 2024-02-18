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

            'e_code' => 'nullable|string',
            'e_code_type' => 'nullable|string',


            'origin_number'  => 'nullable|string',
            'location'  => 'nullable|string',
            'oe_number'  => 'nullable|string',

            'discounts' => 'nullable|array',
            'discounts.s_price.amount' => 'nullable|numeric',
            'discounts.s_price.is_value' => 'nullable|boolean',
            'discounts.s_price.limited' => 'nullable|boolean',
            'discounts.s_price.from' => 'nullable|date',
            'discounts.s_price.to' => 'nullable|date',

            'discounts.d_price.amount' => 'nullable|numeric',
            'discounts.d_price.is_value' => 'nullable|boolean',
            'discounts.d_price.limited' => 'nullable|boolean',
            'discounts.d_price.from' => 'nullable|date',
            'discounts.d_price.to' => 'nullable|date',

            'discounts.sd_price.amount' => 'nullable|numeric',
            'discounts.sd_price.is_value' => 'nullable|boolean',
            'discounts.sd_price.limited' => 'nullable|boolean',
            'discounts.sd_price.from' => 'nullable|date',
            'discounts.sd_price.to' => 'nullable|date',



            's_price'  =>'required|numeric|min:0',
            'd_price'  => 'nullable|numeric|min:0',
            'sd_price'  => 'nullable|numeric|min:0',
            'min_price'  => 'nullable|numeric|min:0',
            'ref_price'  => 'nullable|numeric|min:0',
            'last_cost'  => 'nullable|numeric|min:0',
            'avg_cost'  => 'nullable|numeric',
            'fifo'  =>  'nullable|numeric|min:0',
            'lifo'  =>  'nullable|numeric|min:0',
            'profit_margin'  =>  'nullable|numeric|min:0',
            'warranty'  => 'nullable|numeric',
            'valid_to'  => 'nullable|numeric',
            'barcode'  => 'nullable|string',
            'hs_code'  => 'nullable|string',
            'batch_number'  => 'nullable|string',
            'weight'  =>  'nullable|numeric|min:0',
            'width'  =>  'nullable|numeric|min:0',
            'length'  =>  'nullable|numeric|min:0',
            'height'   =>  'nullable|numeric|min:0',
            'max_limit'  => 'nullable|numeric|min:0',
            'min_limit'  =>  'nullable|numeric|gt:0',
            'reorder_limit'  =>  'nullable|numeric|gt:0',

            'track_stock'  => 'nullable|boolean',
            'require_serial'  => 'nullable|boolean',
            'repeat_serial' => 'nullable|boolean',
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
