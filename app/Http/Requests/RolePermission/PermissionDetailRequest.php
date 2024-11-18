<?php

namespace App\Http\Requests\RolePermission;

use Illuminate\Foundation\Http\FormRequest;

class PermissionDetailRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => __('validation.required', ['attribute' => __('attribute.permission.id')]),
            'id.integer' => __('validation.integer', ['attribute' => __('attribute.permission.id')]),
        ];
    }
}
