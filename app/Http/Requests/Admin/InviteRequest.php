<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class InviteRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => __('validation.required', ['attribute' => __('attribute.admin.email')]),
            'email.email' => __('validation.user.email', ['attribute' => __('attribute.admin.email')]),
        ];
    }
}
