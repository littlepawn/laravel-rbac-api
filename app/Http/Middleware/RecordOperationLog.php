<?php

namespace App\Http\Middleware;

use App\Http\Traits\HttpResponses;
use App\Models\Log\UserOperationLog;
use Closure;

class RecordOperationLog
{
    use HttpResponses;

    public function handle($request, Closure $next)
    {
        $user = auth()->user();
        $response = $next($request); // 先获取响应
        if ($user) {
            $rolesArr = $user->roles->map(function ($role) {
                return $role->only(['slug']);
            });
            UserOperationLog::insert([
                'user_id' => $user->id,
                'role_name' => $rolesArr ? implode('|', $rolesArr->pluck('slug')->toArray()) : '-',
                'path' => $request->path(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'input' => json_encode($request->all()),
                'output' => json_encode($response->getContent()), // 记录响应数据
                'created_at' => now(),
            ]);
        }
        return $response;
    }
}
