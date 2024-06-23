<?php

namespace App\Http\Requests\Inventory;

use App\Enums\Inventory\UnitTypesEnum;
use Illuminate\Foundation\Http\FormRequest;

class StoreUnitRequest extends FormRequest
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

            'units' => 'nullable|array',
            'units.*.name' => 'nullable|string',
            'units.*.code' => 'nullable|string',
            'units.*.type' => 'nullable|in:'.  implode(',',UnitTypesEnum::values()),
            'units.*.ratio' => 'nullable|numeric',
            'units.*.primary' => 'nullable|boolean',

        ];
    }
}
