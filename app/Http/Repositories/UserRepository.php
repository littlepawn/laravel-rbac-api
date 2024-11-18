<?php

namespace App\Http\Repositories;

use App\Constants\CommonConstants;
use App\Constants\response\UserConstants;
use App\Exceptions\CommonException;
use App\Http\Repositories\Interface\UserRepositoryInterface;
use App\Http\Traits\HttpResponses;
use App\Models\User\Role;
use App\Models\User\User;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    use HttpResponses;

    /**
     * 获取登录token失效时长
     */
    public function getTokenExpireIn(): int
    {
        return CommonConstants::LOGIN_TOKEN_VALID_MINUTES;
    }

    /**
     * 根据邮箱查找用户
     * @param string $email
     * @param bool $throwException
     * @return User|null
     * @throws CommonException
     */
    public function findByEmail(string $email, bool $throwException = true): ?User
    {
        $user = User::where('email', $email)->first();
        // 用户未找到
        if ($throwException && empty($user)) $this->throwException(UserConstants::USER_NOT_EXIST);
        return $user;
    }

    /**
     * 根据id查找用户
     * @param int $id
     * @param bool $throwException
     * @return User|null
     * @throws CommonException
     */
    public function findById(int $id, bool $throwException = true): ?User
    {
        $user = User::find($id);
        // 用户未找到
        if ($throwException && empty($user)) $this->throwException(UserConstants::USER_NOT_EXIST);
        return $user;
    }

    /**
     * 判断邮箱是否未验证
     * @param $user
     * @return bool
     */
    public function isEmailNotVerified($user): bool
    {
        return is_null($user->email_verified_at);
    }

    /**
     * 判断用户是否被锁定
     * @param $user
     * @return bool
     */
    public function isLocked($user): bool
    {
        return (bool)$user->is_locked;
    }

    /**
     * 判断用户是否被封禁
     * @param $user
     * @return bool
     */
    public function isBanned($user): bool
    {
        return (bool)$user->is_banned;
    }

    /**
     * 判断密码是否错误
     * @param $password
     * @param $user
     * @return bool
     */
    public function isPasswordIncorrect($password, $user): bool
    {
        return !Hash::check($password, $user->password);
    }

    /**
     * 创建用户
     * @param array $data
     * @return User
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * 分配角色
     * @param User $user
     * @param string $role
     * @return void
     */
    public function assignRole($user, string $role)
    {
        $role = Role::where('slug', $role)->first();
        if ($role) {
            $user->roles()->attach($role->id, ['created_at' => now()]);
        }
    }

    /**
     * 获取用户权限
     * @param User $user
     * @return mixed
     */
    public function getUserPermissions(User $user): mixed
    {
        $user->load('roles.permissions');

        // 获取用户通过角色获得的权限
        $rolePermissions = $user->roles->flatMap(function ($role) {
            return $role->permissions->map(function ($permission) {
                return [
                    'name' => $permission->name,
                    'slug' => $permission->slug,
                ];
            });
        });

        // 获取用户直接拥有的权限
        $userPermissions = $user->permissions()->get()->map(function ($permission) {
            return [
                'name' => $permission->name,
                'slug' => $permission->slug,
            ];
        });

        // 合并角色权限和直接权限，并去重
        return $rolePermissions->merge($userPermissions)
            ->unique('slug')
            ->values();
    }
}
