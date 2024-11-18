<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class EmailVerifyRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'token' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('validation.required', ['attribute' => __('attribute.user.email')]),
            'email.email' => __('validation.user.email', ['attribute' => __('attribute.user.email')]),
            'token.required' => __('validation.required', ['attribute' => __('attribute.user.token')]),
        ];
    }
}
