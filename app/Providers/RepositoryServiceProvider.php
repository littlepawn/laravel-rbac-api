<?php

namespace App\Providers;

use App\Http\Repositories\AdminRepository;
use App\Http\Repositories\Interface\UserRepositoryInterface;
use App\Http\Repositories\UserRepository;
use App\Http\Services\AdminService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // 默认绑定
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        // 上下文绑定
        $this->app->when(AdminService::class)
            ->needs(UserRepositoryInterface::class)
            ->give(AdminRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //

    }
}
