<?php
return [
    'user' => [
        'id' => '用户ID[user_id]',
        'name' => '名称[name]',
        'email' => '电子邮件[email]',
        'password' => '密码[password]',
        'token' => '[token]',
        'old_password' => '旧密码[old_password]',
    ],
    'admin' => [
        'id' => '管理员ID[admin_id]',
        'name' => '管理员名称[name]',
        'is_banned' => '是否封禁[is_banned]',
        'email' => '电子邮件[email]'
    ],
    'role' => [
        'id' => '角色ID[role_id]',
        'name' => '角色名称[name]',
        'slug' => '角色标识[slug]',
        'remark' => '备注[remark]',
    ],
    'permission' => [
        'id' => '权限ID[permission_id]',
        'name' => '权限名称[name]',
        'slug' => '权限标识[slug]',
        'uri' => '权限URI[uri]',
        'parent_id' => '父级权限ID[parent_id]',
        'remark' => '备注[remark]',
    ],
    'log' => [
        'user_id' => '用户ID[user_id]',
        'role_keywords' => '角色名称[role_keywords]',
        'path' => '路径[path]',
        'method' => '方法[method]',
        'ip' => 'IP地址[ip]',
        'device' => '设备[device]',
    ],
    'page' => '页码[page]',
    'per_page' => '每页数量[per_page]',
    'start_date' => '开始日期[start_date]',
    'end_date' => '结束日期[end_date]',
];
