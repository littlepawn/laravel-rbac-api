<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('validation.user.required', ['attribute' => __('attribute.user.email')]),
            'email.email' => __('validation.user.email', ['attribute' => __('attribute.user.email')]),
            'password.required' => __('validation.user.required', ['attribute' => __('attribute.user.password')]),
        ];
    }
}
