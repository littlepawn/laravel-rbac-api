<?php

namespace App\Constants\response;

use App\Http\Traits\ConstantsHelper;

final class RolePermissionConstants
{
    use ConstantsHelper;

    public const ROLE_NOT_EXIST = 1101;
    public const ROLE_EXIST = 1102;
    public const ROLE_RELATED_USER = 1103;
    public const PARENT_PERMISSION_NOT_FOUND = 1104;
    public const PERMISSION_NOT_FOUND = 1105;
    public const PERMISSION_EXIST = 1106;
    public const PERMISSION_RELATED_UPDATE = 1107;
    public const PERMISSION_RELATED_DELETE = 1108;
    public const PERMISSION_HAS_CHILDREN = 1109;
    public const PERMISSION_IDS_NOT_FOUND = 1110;
    public const PARENT_PERMISSION_NOT_EXIST = 1111;

}
