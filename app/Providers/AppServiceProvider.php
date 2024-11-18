<?php

namespace App\Providers;

use App\Http\Services\LoginService;
use App\Http\Services\PermissionService;
use App\Http\Services\RegisterService;
use App\Http\Services\RoleService;
use App\Http\Services\UserService;
use App\Http\Services\AdminService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //注册service服务
        $this->app->singleton(LoginService::class);
        $this->app->singleton(RegisterService::class);
        $this->app->singleton(UserService::class);
        $this->app->singleton(AdminService::class);
        $this->app->singleton(RoleService::class);
        $this->app->singleton(PermissionService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
