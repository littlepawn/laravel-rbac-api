<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\PageRequest;

class OperationLogPageRequest extends PageRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            'role_keywords' => 'nullable|string|max:16',
            'user_id' => 'nullable|integer',
            'method' => 'nullable|in:GET,POST,PUT,DELETE',
            'ip' => 'nullable|ip',
        ]);
    }

    public function messages()
    {
        return array_merge(parent::messages(), [
            'role_keywords.string' => __('validation.string', ['attribute' => __('attribute.log.role_keywords')]),
            'role_keywords.max.string' => __('validation.max.string', ['attribute' => __('attribute.log.role_keywords'), 'max' => 16]),
            'user_id.integer' => __('validation.integer', ['attribute' => __('attribute.log.user_id')]),
            'method.in' => __('validation.in', ['attribute' => __('attribute.log.method'), 'in' => 'GET,POST,PUT,DELETE']),
            'ip.ip' => __('validation.ip', ['attribute' => __('attribute.log.ip')]),
        ]);
    }
}
