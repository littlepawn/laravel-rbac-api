<?php

namespace App\Http\Middleware;

use App\Constants\response\UserConstants;
use App\Http\Traits\HttpResponses;
use App\Models\User\Permission;
use Closure;

class CheckPermission
{
    use HttpResponses;

    public function handle($request, Closure $next, ...$permissions)
    {
        $user = auth()->user();

        if (empty($user)) return $this->error(UserConstants::USER_UNAUTHORIZED);

        // 管理员跳过所有验证
        if ($user->is_admin) {
            return $next($request);
        }

        // $permissions有设置检查权限
        if ($this->hasPermissions($user, $permissions)) {
            return $next($request);
        }

        // 检查当前路由和permission uri字段是否匹配来鉴权
        $uriPermission = $this->getPermissionByUri($request);
        if ($uriPermission && $this->checkPermission($user, $uriPermission)) {
            return $next($request);
        }

        return $this->error(UserConstants::USER_NO_PERMISSION);
    }

    /**
     * 获取当前路由对应的权限
     * @param $request
     * @return null
     */
    protected function getPermissionByUri($request)
    {
        $currentRoute = $request->route();
        $currentUri = strtolower(preg_replace('/^api\//', '', $currentRoute->uri));
        $permission = Permission::where('uri', $currentUri)->first();
        if ($permission) {
            return $permission->slug;
        } else {
            return null;
        }
    }

    /**
     * 检查用户是否有权限组中的所有权限
     * @param $user
     * @param $permissions
     * @return bool
     */
    private function hasPermissions($user, $permissions): bool
    {
        $hasAllPermissions = true;
        if (empty($permissions)) return false;
        foreach ($permissions as $permission) {
            // 权限为admin时，只有管理员才有权限
            if ($permission === 'admin' && $user->is_admin) {
                return true;
            }
            // 单一权限检查通过就返回true
//            if ($this->checkPermission($user, $permission)) {
//                return true;
//            }
            if (!$this->checkPermission($user, $permission)) {
                $hasAllPermissions = false;
                break;
            }
        }

        return $hasAllPermissions;
    }

    /**
     * 检查用户是否具有指定权限
     * @param $user
     * @param $permission
     */
    private function checkPermission($user, $permission)
    {
        return $user->hasPermission($permission);
    }
}
