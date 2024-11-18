<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\PageRequest;

class ListPageRequest extends PageRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            'email' => 'nullable|email',
            'is_locked' => 'nullable|in:0,1',
        ]);
    }

    public function messages()
    {
        return array_merge(parent::messages(), [
            'email.email' => __('validation.user.email', ['attribute' => __('attribute.user.email')]),
            'is_locked.in' => __('validation.page.is_locked'),
        ]);
    }
}
