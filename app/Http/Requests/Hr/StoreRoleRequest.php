<?php

namespace App\Http\Requests\Hr;

use App\Models\System\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('api')->user()->can('roles.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'name'=>['required', Rule::unique('roles','name')->ignore($this->id),],
            'permissions'=>'required|array',
            'permissions.*.id'=>'nullable|exists:permissions,id',
            'permissions.*.checked'=>'nullable|boolean',
        ];
    }
    public function prepareForValidation()
    {
        if ($this->slug){
            $this->merge(['id' => Role::where('slug',$this->slug)->value('id')
            ]);
        }

    }
}
