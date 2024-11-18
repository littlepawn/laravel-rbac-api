<?php

namespace App\Http\Requests\RolePermission;

use Illuminate\Foundation\Http\FormRequest;

class PermissionCreateRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'slug' => 'required|string|min:1|max:32',
            'uri' => 'nullable|string|min:1|max:32',
            'parent_id' => 'nullable|integer',
            'remark' => 'nullable|string|min:1|max:32',
        ];
    }

    public function messages()
    {
        return [
            'remark.string' => __('validation.string', ['attribute' => __('attribute.permission.remark')]),
            'remark.min' => __('validation.min.string', ['attribute' => __('attribute.permission.remark'), 'min' => 1]),
            'remark.max' => __('validation.max.string', ['attribute' => __('attribute.permission.remark'), 'max' => 32]),
            'slug.required' => __('validation.required', ['attribute' => __('attribute.permission.slug')]),
            'slug.string' => __('validation.string', ['attribute' => __('attribute.permission.slug')]),
            'slug.min' => __('validation.min.string', ['attribute' => __('attribute.permission.slug'), 'min' => 1]),
            'slug.max' => __('validation.max.string', ['attribute' => __('attribute.permission.slug'), 'max' => 32]),
            'uri.string' => __('validation.string', ['attribute' => __('attribute.permission.uri')]),
            'uri.min' => __('validation.min.string', ['attribute' => __('attribute.permission.uri'), 'min' => 1]),
            'uri.max' => __('validation.max.string', ['attribute' => __('attribute.permission.uri'), 'max' => 32]),
            'parent_id.integer' => __('validation.integer', ['attribute' => __('attribute.permission.parent_id')]),
        ];
    }
}
