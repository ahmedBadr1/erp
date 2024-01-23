<?php

namespace App\Http\Requests\Accounting;

use App\Models\Accounting\CostCenterNode;
use App\Models\Accounting\Node;
use Illuminate\Foundation\Http\FormRequest;

class StoreCostCenterRequest extends FormRequest
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
            'name' => 'required|string',
            'description' => 'nullable|string',

            'cost_center_node_id' => 'required|exists:nodes,id',

            'groups.*' => 'nullable|exists:groups,id',
            'users.*' => 'nullable|exists:users,id',
            'tags.*' => 'nullable|string',

//            'active' => 'required|boolean',
        ];
    }
    public function prepareForValidation()
    {
        $this->merge([
            'cost_center_node_id' => CostCenterNode::where('code',$this->node)->value('id')
        ]);
    }
}
