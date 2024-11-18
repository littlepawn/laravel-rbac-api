<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'token' => 'required',
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
            'token.required' => __('validation.required', ['attribute' => __('attribute.user.token')]),
            'email.required' => __('validation.required', ['attribute' => __('attribute.user.email')]),
            'email.email' => __('validation.user.email', ['attribute' => __('attribute.user.email')]),
            'password.required' => __('validation.user.required', ['attribute' => __('attribute.user.password')]),
            'password.min' => __('validation.user.min.string', ['attribute' => __('attribute.user.password'), 'min' => 6]),
            'password.max' => __('validation.user.max.string', ['attribute' => __('attribute.user.password'), 'max' => 32]),
            'password.regex' => __('validation.user.regex', ['attribute' => __('attribute.user.password')]),
        ];
    }
}
