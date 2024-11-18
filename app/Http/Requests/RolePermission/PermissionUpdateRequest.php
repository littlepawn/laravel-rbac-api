<?php

namespace App\Http\Requests\RolePermission;

use Illuminate\Foundation\Http\FormRequest;

class PermissionUpdateRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|integer',
            'parent_id' => 'nullable|integer|min:1|different:id',
            'remark' => 'nullable|string|min:1|max:32',
            'slug' => 'nullable|string|min:1|max:32',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => __('validation.required', ['attribute' => __('attribute.permission.id')]),
            'id.integer' => __('validation.integer', ['attribute' => __('attribute.permission.id')]),
            'parent_id.integer' => __('validation.integer', ['attribute' => __('attribute.permission.parent_id')]),
            'parent_id.min' => __('validation.min.numeric', ['attribute' => __('attribute.permission.parent_id')]),
            'parent_id.different' => __('validation.different', ['attribute' => __('attribute.permission.parent_id'), 'other' => __('attribute.permission.id')]),
            'remark.string' => __('validation.string', ['attribute' => __('attribute.permission.remark')]),
            'remark.min' => __('validation.min.string', ['attribute' => __('attribute.permission.remark'), 'min' => 1]),
            'remark.max' => __('validation.max.string', ['attribute' => __('attribute.permission.remark'), 'max' => 32]),
            'slug.string' => __('validation.string', ['attribute' => __('attribute.permission.slug')]),
            'slug.min' => __('validation.min.string', ['attribute' => __('attribute.permission.slug'), 'min' => 1]),
            'slug.max' => __('validation.max.string', ['attribute' => __('attribute.permission.slug'), 'max' => 32]),

        ];
    }
}
