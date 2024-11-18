<?php

namespace App\Http\Requests;

use App\Constants\CommonConstants;
use Illuminate\Foundation\Http\FormRequest;

class PageRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:' . CommonConstants::PAGE_MAX_SIZE,
        ];
    }

    public function messages()
    {
        return [
            'page.integer' => __('validation.page.integer', ['attribute' => __('attribute.page')]),
            'page.min' => __('validation.page.min'),
            'per_page.integer' => __('validation.page.integer', ['attribute' => __('attribute.per_page')]),
            'per_page.min' => __('validation.page.per_page_min'),
            'per_page.max' => __('validation.page.per_page_max', ['per_page_max' => CommonConstants::PAGE_MAX_SIZE]),
        ];
    }
}
