<?php

namespace App\Constants;

class CommonConstants
{
    // 密码最大重试次数
    public const PASSWORD_MAX_RETRY_TIMES = 6;

    // 登录token有效期（分钟）
    public const LOGIN_TOKEN_VALID_MINUTES = 15;

    // 分页大小
    public const PAGE_SIZE = 10;
    public const PAGE_MAX_SIZE = 100;

    //邮箱验证码文件名
    public const EMAIL_VERIFY_FILENAME = 'email_verify.json';
    public const EMAIL_INVITE_FILENAME = 'email_invite.json';
    public const PASSWORD_RESET_FILENAME = 'password_reset.json';

}
