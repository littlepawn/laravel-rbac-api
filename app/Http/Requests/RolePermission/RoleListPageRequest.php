<?php

namespace App\Http\Requests\RolePermission;

use App\Http\Requests\PageRequest;

class RoleListPageRequest extends PageRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            'name' => 'nullable',
        ]);
    }

    public function messages()
    {
        return array_merge(parent::messages(), [

        ]);
    }
}
