<?php
return [
    'required' => ':attribute 字段是必需的。',
    'integer' => ':attribute 必须是整数。',
    'string' => ':attribute 必须是一个字符串。',
    'in' => ':attribute 必须在 :in 中。',
    'different' => ':attribute 和 :other 必须不同。',
    'array' => ':attribute 必须是一个数组。',
    'excel_file' => ':attribute 必须是excel文件。',
    'date' => ':attribute 必须是一个日期。',
    'ip' => ':attribute 必须是一个有效的IP地址。',
    'url' => ':attribute 必须是一个有效的URL。',
    'min' => [
        'string' => ':attribute 必须至少是 :min 个字符。',
        'numeric' => ':attribute 必须大于或等于 :min。',
    ],
    'max' => [
        'string' => ':attribute 不能超过 :max 个字符。',
        'numeric' => ':attribute 必须小于或等于 :max。',
    ],
    'user' => [
        'required' => ':attribute 字段是必需的。',
        'min' => [
            'string' => ':attribute 必须至少是 :min 个字符。',
        ],
        'max' => [
            'string' => ':attribute 不能超过 :max 个字符。',
        ],
        'email' => ':attribute 必须是一个有效的电子邮件地址。',
        'unique' => ':attribute 已经被占用。',
        'regex' => ':attribute 必须包含至少一个大写字母、一个小写字母、一个数字和一个特殊字符。',
        'old_password' => ':attribute 不匹配。',
    ],
    'date_format' => [
        'after_or_equal' => ':attribute 必须在 :date 之后或等于 :date。',
    ],
    'page' => [
        'integer' => ':attribute 必须是整数。',
        'min' => '页码必须大于等于1。',
        'per_page_min' => '每页数量必须大于等于1。',
        'per_page_max' => '每页数量必须小于等于 :per_page_max。',
        'role_filter' => '角色筛选字段不合法, 可选 admin, other, all。',
        'is_locked' => '锁定状态字段不合法, 可选 0, 1。',
    ]
];
