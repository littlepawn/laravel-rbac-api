<?php

namespace App\Http\Requests\RolePermission;

use Illuminate\Foundation\Http\FormRequest;

class PermissionTreeRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'slug' => 'nullable',
        ];
    }

    public function messages()
    {
        return [];
    }
}
