<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class InviteConfirmRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users,email',
            'token' => 'required',
            'name' => 'required|min:2|max:32',
            'password' => [
                'required',
                'min:6',
                'max:32',
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*?&]/'   // must contain a special character
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('validation.required', ['attribute' => __('attribute.user.email')]),
            'email.email' => __('validation.user.email', ['attribute' => __('attribute.user.email')]),
            'email.unique' => __('validation.user.unique', ['attribute' => __('attribute.user.email')]),
            'token.required' => __('validation.required', ['attribute' => __('attribute.user.token')]),
            'name.required' => __('validation.user.required', ['attribute' => __('attribute.user.name')]),
            'name.min' => __('validation.user.min.string', ['attribute' => __('attribute.user.name'), 'min' => 2]),
            'name.max' => __('validation.user.max.string', ['attribute' => __('attribute.user.name'), 'min' => 32]),
            'password.required' => __('validation.user.required', ['attribute' => __('attribute.user.password')]),
            'password.min' => __('validation.user.min.string', ['attribute' => __('attribute.user.password'), 'min' => 6]),
            'password.max' => __('validation.user.max.string', ['attribute' => __('attribute.user.password'), 'max' => 32]),
            'password.regex' => __('validation.user.regex', ['attribute' => __('attribute.user.password')]),
        ];
    }
}
