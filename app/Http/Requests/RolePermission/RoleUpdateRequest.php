<?php

namespace App\Http\Requests\RolePermission;

use Illuminate\Foundation\Http\FormRequest;

class RoleUpdateRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|integer',
            'slug' => 'string|min:1|max:32',
            'remark' => 'nullable|string|min:1|max:32',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => __('validation.required', ['attribute' => __('attribute.role.id')]),
            'id.integer' => __('validation.integer', ['attribute' => __('attribute.role.id')]),
            'slug.string' => __('validation.string', ['attribute' => __('attribute.role.slug')]),
            'slug.min' => __('validation.min.string', ['attribute' => __('attribute.role.slug')]),
            'slug.max' => __('validation.max.string', ['attribute' => __('attribute.role.slug')]),
            'remark.string' => __('validation.string', ['attribute' => __('attribute.role.remark')]),
            'remark.min' => __('validation.min.string', ['attribute' => __('attribute.role.remark')]),
            'remark.max' => __('validation.max.string', ['attribute' => __('attribute.role.remark')]),
        ];
    }
}
