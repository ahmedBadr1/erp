<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInveitationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true ;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|max:255|email|unique:invitations|unique:users',
             'role_id'  => ['nullable', 'exists:roles,id'],
        ];
    }

    /**
     * Custom error messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.unique:invitations' => 'Invitation with this email address already requested.',
            'email.unique:users' => ' User with this email address already existed.',
                'role_id.exists:roles' => 'Role Does not existed.'

        ];
    }
}
