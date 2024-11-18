<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ModifyRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'admin_id' => 'required|integer',
            'name' => 'nullable|min:2|max:32',
            'is_banned' => 'nullable|in:0,1'
        ];
    }

    public function messages()
    {
        return [
            'admin_id.required' => __('validation.required', ['attribute' => __('attribute.admin.id')]),
            'admin_id.integer' => __('validation.integer', ['attribute' => __('attribute.admin.id')]),
            'name.min' => __('validation.user.min.string', ['attribute' => __('attribute.admin.name'), 'min' => 2]),
            'name.max' => __('validation.user.max.string', ['attribute' => __('attribute.admin.name'), 'max' => 32]),
            'is_banned.in' => __('validation.in', ['attribute' => __('attribute.admin.is_banned'), 'in' => '0,1']),
        ];
    }
}
