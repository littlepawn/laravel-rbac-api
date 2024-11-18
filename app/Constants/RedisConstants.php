<?php

namespace App\Constants;

class RedisConstants
{
    public const EMAIL_VERIFY_CODE_PREFIX = 'email:verify:code:';
    public const EMAIL_INVITE_TOKEN_PREFIX = 'email:invite:token:';
    public const EMAIL_VERIFY_CODE_EXPIRE_TIME = 86400;
    public const EMAIL_INVITE_ADMIN_EXPIRE_TIME = 86400 * 3;

    public static function geneRedisKey(string $prefix, string ...$key): string
    {
        return $prefix . implode(':', $key);
    }
}
