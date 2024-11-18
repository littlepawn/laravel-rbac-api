<?php

namespace App\Http\Middleware;

use App\Constants\response\UserConstants;
use App\Http\Traits\HttpResponses;
use Closure;

class CheckRole
{
    use HttpResponses;

    public function handle($request, Closure $next, ...$roles)
    {
        $user = auth()->user();
        if (empty($user)) return $this->error(UserConstants::USER_UNAUTHORIZED);

        if ($this->hasRole($user, $roles)) {
            return $next($request);
        }

        return $this->error(UserConstants::USER_NO_ROLE);
    }

    // 检查用户是否有权限
    private function hasRole($user, $roles): bool
    {
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return true;
            }
        }

        return false;
    }
}
