<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AssignRoleRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'required|integer',
            'role_ids' => 'required|array',
            'role_ids.*' => 'integer',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => __('validation.required', ['attribute' => __('attribute.user.id')]),
            'user_id.integer' => __('validation.integer', ['attribute' => __('attribute.user.id')]),
            'role_ids.required' => __('validation.required', ['attribute' => __('attribute.role.id')]),
            'role_ids.array' => __('validation.array', ['attribute' => __('attribute.role.id')]),
            'role_ids.*.integer' => __('validation.integer', ['attribute' => __('attribute.role.id')]),
        ];
    }
}
