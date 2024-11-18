<?php

namespace App\Http\Requests\RolePermission;

use Illuminate\Foundation\Http\FormRequest;

class RoleRevokePermissionRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|integer',
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'integer',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => __('validation.required', ['attribute' => __('attribute.role.id')]),
            'id.integer' => __('validation.integer', ['attribute' => __('attribute.role.id')]),
            'permission_ids.required' => __('validation.required', ['attribute' => __('attribute.permission.id')]),
            'permission_ids.array' => __('validation.array', ['attribute' => __('attribute.permission.id')]),
            'permission_ids.*.integer' => __('validation.integer', ['attribute' => __('attribute.permission.id')]),
        ];
    }
}
