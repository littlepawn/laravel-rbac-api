<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ModifyNameRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|min:2|max:32',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.user.required', ['attribute' => __('attribute.user.name')]),
            'name.min' => __('validation.user.min.string', ['attribute' => __('attribute.user.name'), 'min' => 2]),
            'name.max' => __('validation.user.max.string', ['attribute' => __('attribute.user.name'), 'min' => 32]),
        ];
    }
}
