<?php

namespace App\Http\Traits;


use Illuminate\Support\Facades\Log;

trait LogPermission
{
    private function logPermissionResult(string $permission, array $checkedPermissions, bool $hasPermission): void
    {
        if (1 === count($checkedPermissions)) {
            $this->logPermission("验权: user-{$this->id} [{$permission}] " . ($hasPermission ? '有当前权限' : '无当前权限'));
        } else {
            $this->logPermission("验权: user-{$this->id} [{$permission}] " . ($hasPermission ? '父级有权限' : '父级无权限'));
        }
    }

    private function logPermissionStart(string $permission): void
    {
        $this->logPermission("==========验权开始: user-{$this->id} [{$permission}] ==========");
    }

    private function logPermissionEnd(string $permission): void
    {
        $this->logPermission("==========验权结束: user-{$this->id} [{$permission}] ==========");
    }

    private function logPermission($log)
    {
        if (config("app.switch.permission_log")) {
            Log::channel("permission")->info($log);
        }
    }
}
