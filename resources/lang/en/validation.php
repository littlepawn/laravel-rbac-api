<?php
return [
    'required' => ':attribute field is required.',
    'integer' => ':attribute must be an integer.',
    'string' => ':attribute must be a string.',
    'in' => ':attribute must be in :in.',
    'different' => ':attribute and :other must be different.',
    'array' => ':attribute must be an array.',
    'excel_file' => ':attribute must be an excel file.',
    'date' => ':attribute must be a date.',
    'ip' => ':attribute must be a valid IP address.',
    'url' => ':attribute must be a valid URL.',
    'min' => [
        'string' => ':attribute must be at least :min characters.',
        'numeric' => ':attribute must be greater than or equal to :min.',
    ],
    'max' => [
        'string' => ':attribute may not be greater than :max characters.',
        'numeric' => ':attribute must be less than or equal to :max.',
    ],
    'user' => [
        'required' => ':attribute field is required.',
        'min' => [
            'string' => ':attribute must be at least :min characters.',
        ],
        'max' => [
            'string' => ':attribute may not be greater than :max characters.',
        ],
        'email' => ':attribute must be a valid email address.',
        'unique' => ':attribute has already been taken.',
        'regex' => ':attribute must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        'old_password' => ':attribute does not match.',
    ],
    'date_format' => [
        'after_or_equal' => ':attribute must be after or equal to :date.',
    ],
    'page' => [
        'integer' => ':attribute must be an integer.',
        'min' => 'page must be greater than or equal to 1.',
        'per_page_min' => 'per page must be greater than or equal to 1.',
        'per_page_max' => 'per page must be less than or equal to :per_page_max.',
        'role_filter' => 'Role filter field is illegal, optional admin, other, all.',
        'is_locked' => 'Lock status field is illegal, optional 0, 1.',
    ]
];
