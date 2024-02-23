<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DownloadTemplateRequest extends FormRequest
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
        $templates = ['products','warehouses','brands','accounts','costCenters','suppliers','clients','services','groups','users'];
        $types = ['csv','xlsx'];

        return [
            'name' => ['required',Rule::in($templates)],
            'type' =>['required',Rule::in($types)],
        ];
    }
}
