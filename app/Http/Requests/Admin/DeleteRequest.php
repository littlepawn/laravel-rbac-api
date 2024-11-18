<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DeleteRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'admin_id' => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'admin_id.required' => __('validation.required', ['attribute' => __('attribute.admin.id')]),
            'admin_id.integer' => __('validation.integer', ['attribute' => __('attribute.admin.id')]),
        ];
    }
}
