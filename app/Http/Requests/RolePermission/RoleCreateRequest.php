<?php

namespace App\Http\Requests\RolePermission;

use Illuminate\Foundation\Http\FormRequest;

class RoleCreateRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'remark' => 'nullable|string|min:1|max:32',
            'slug' => 'required|string|min:1|max:32',
        ];
    }

    public function messages()
    {
        return [
            'remark.string' => __('validation.string', ['attribute' => __('attribute.role.remark')]),
            'remark.min' => __('validation.min.string', ['attribute' => __('attribute.role.remark'), 'min' => 1]),
            'remark.max' => __('validation.max.string', ['attribute' => __('attribute.role.remark'), 'max' => 32]),
            'slug.required' => __('validation.required', ['attribute' => __('attribute.role.slug')]),
            'slug.string' => __('validation.string', ['attribute' => __('attribute.role.slug')]),
            'slug.min' => __('validation.min.string', ['attribute' => __('attribute.role.slug'), 'min' => 1]),
            'slug.max' => __('validation.max.string', ['attribute' => __('attribute.role.slug'), 'max' => 32]),
        ];
    }
}
