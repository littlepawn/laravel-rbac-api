<?php

namespace App\Models\User;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Traits\HasPermissions;
use App\Http\Traits\LogPermission;
use App\Http\Traits\ModelTime;
use App\Observers\UserObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasPermissions, ModelTime, LogPermission;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_locked',
        'is_banned',
        'is_admin',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * 应用程序的模型观察者。
     *
     * @var array
     */
    protected $observers = [
        User::class => [UserObserver::class],
    ];


    /**
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /**
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permission');
    }


    public function hasRole($role)
    {
        if ($this->roles()->where('slug', $role)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * @param $permission
     * @return bool
     */
    public function hasPermission($permission): bool
    {
        $this->logPermissionStart($permission);
        return $this->hasPermissionAndParents($permission);
    }

    /**
     * 检查用户角色组是否具有指定权限
     * @param $permission
     * @return bool
     */
    private function hasPermissionByRole($permission): bool
    {
        foreach ($this->roles as $role) {
            if ($role->permissions()->where('slug', $permission)->exists()) {
                return true;
            }
        }
        return false;
    }

    /**
     * 检查单个权限，要查看是否满足有所有父权限你
     * @param $permission
     * @param array $checkedPermissions
     * @return bool
     */
    private function hasPermissionAndParents($permission, $checkedPermissions = []): bool
    {
        // 避免循环引用
        if (in_array($permission, $checkedPermissions)) {
            return false;
        }

        // 增加当前权限到已检查权限列表
        $checkedPermissions[] = $permission;

        // 检查用户是否具有指定权限
        if ($this->hasPermissionByRole($permission) || $this->permissions()->where('slug', $permission)->exists()) {
            // 查询当前权限的父权限
            $currentPermission = Permission::where('slug', $permission)
                ->with('parent')
                ->first();

            $this->logPermissionResult($permission, $checkedPermissions, true);

            // 如果有父权限，则递归检查父权限
            if ($currentPermission->parent) {
                return $this->hasPermissionAndParents($currentPermission->parent->slug, $checkedPermissions);
            }
            $this->logPermissionEnd($permission);
            return true;
        }

        $this->logPermissionResult($permission, $checkedPermissions, false);
        $this->logPermissionEnd($permission);

        return false;
    }

    protected static function booted()
    {
        static::deleting(function ($model) {
            Log::info('User deleted: ' . $model->email . ' timestamp: ' . now());
        });
    }


}
