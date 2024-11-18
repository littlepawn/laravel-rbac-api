<?php

namespace App\Constants\response;

use App\Http\Traits\ConstantsHelper;

final class UserConstants
{
    use ConstantsHelper;

    public const USER_VALIDATION = 1001;
    public const USER_UNAUTHORIZED = 1002;
    public const USER_NO_PERMISSION = 1003;
    public const USER_NOT_EXIST = 1004;
    public const USER_PASSWORD_REPEATED = 1005;
    public const USER_PASSWORD_NO_MATCH = 1006;
    public const USER_PASSWORD_RETRY_MAX = 1007;
    public const USER_LOCKED = 1008;
    public const USER_BANNED = 1009;
    public const USER_EMAIL_VERIFY_FAILED = 1010;
    public const USER_EMAIL_NOT_VERIFY = 1011;
    public const USER_INVALID_RESET_TOKEN = 1012;
    public const USER_RESET_PASSWORD_FAILED = 1013;
    public const USER_LOGIN_PASSWORD_NO_MATCH = 1014;
    public const USER_EMAIL_NOT_EXIST = 1015;
    public const USER_EMAIL_VERIFY_URL_EXPIRED = 1016;
    public const ADMIN_NOT_FOUND = 1017;
    public const ADMIN_CANNOT_DELETE_SELF = 1018;
    public const ADMIN_EMAIL_INVITE_URL_EXPIRED = 1019;
    public const ADMIN_EMAIL_INVITE_FAILED = 1020;
    public const USER_NO_ROLE = 1021;
    public const ADMIN_CANNOT_BAN_SELF = 1022;
    public const ASSIGN_ROLES_NOT_EXIST = 1023;
    public const ASSIGN_PERMISSIONS_NOT_EXIST = 1024;
    public const ADMIN_CANNOT_ASSIGN_SELF = 1025;
    public const ADMIN_CANNOT_REVOKE_SELF = 1026;

}
