<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RevokePermissionRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'required|integer',
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'integer',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => __('validation.required', ['attribute' => __('attribute.user.id')]),
            'user_id.integer' => __('validation.integer', ['attribute' => __('attribute.user.id')]),
            'permission_ids.required' => __('validation.required', ['attribute' => __('attribute.permission.id')]),
            'permission_ids.array' => __('validation.array', ['attribute' => __('attribute.permission.id')]),
            'permission_ids.*.integer' => __('validation.integer', ['attribute' => __('attribute.permission.id')]),
        ];
    }
}
