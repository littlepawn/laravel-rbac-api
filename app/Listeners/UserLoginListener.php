<?php

namespace App\Listeners;

use App\Events\UserLoginEvent;
use App\Models\Log\UserLoginLog;
use App\Models\User\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class UserLoginListener implements ShouldQueue
{
    /**
     * @param UserLoginEvent $event
     * @return void
     */
    public function handle(UserLoginEvent $event): void
    {
//        Log::info('用户登录触发事件');
        $loginInfo = $event->loginInfo;
        /** @var User $user */
        $user = $loginInfo['user'];
        //记录登录日志
        try {
            $rolesArr = $user->roles->map(function ($role) {
                return $role->only(['slug']);
            });
            UserLoginLog::insert([
                'user_id' => $user->id,
                'role_name' => $rolesArr ? implode('|', $rolesArr->pluck('slug')->toArray()) : '-',
                'ip' => $loginInfo['ip'],
                'device' => $loginInfo['device'],
                'created_at' => $loginInfo['login_at'],
            ]);
            echo "Login log saved successfully.\n";
        } catch (\Throwable $e) {
            Log::error("Error login log: " . $e->getMessage());
        }
    }
}
