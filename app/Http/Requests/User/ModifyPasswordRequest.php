<?php

namespace App\Http\Requests\User;

use App\Rules\User\PasswordVerify;
use Illuminate\Foundation\Http\FormRequest;

class ModifyPasswordRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'password' => [
                'required',
                'min:6',
                'max:32',
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*?&]/'   // must contain a special character
            ],
            'old_password' => ['required', 'min:6', 'max:32', new PasswordVerify()],
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => __('validation.user.required', ['attribute' => __('attribute.user.password')]),
            'password.min' => __('validation.user.min.string', ['attribute' => __('attribute.user.password'), 'min' => 6]),
            'password.max' => __('validation.user.max.string', ['attribute' => __('attribute.user.password'), 'max' => 32]),
            'password.regex' => __('validation.user.regex', ['attribute' => __('attribute.user.password')]),
            'old_password.required' => __('validation.user.required', ['attribute' => __('attribute.user.old_password')]),
            'old_password.min' => __('validation.user.min.string', ['attribute' => __('attribute.user.old_password'), 'min' => 6]),
            'old_password.max' => __('validation.user.max.string', ['attribute' => __('attribute.user.old_password'), 'max' => 32]),
        ];
    }
}
