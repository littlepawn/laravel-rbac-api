<?php

namespace App\Http\Repositories;

use App\Constants\response\RolePermissionConstants;
use App\Constants\response\UserConstants;
use App\Constants\ResponseConstants;
use App\Exceptions\CommonException;
use App\Http\Repositories\Interface\UserRepositoryInterface;
use App\Http\Traits\CommonRepository;
use App\Http\Traits\HttpResponses;
use App\Models\User\Permission;
use App\Models\User\Role;
use App\Models\User\User;
use Illuminate\Support\Facades\Hash;

class AdminRepository implements UserRepositoryInterface
{
    use HttpResponses, CommonRepository;

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
        $admin = User::where('id', $id)->where('is_admin', 1)->first();
        // 用户未找到
        if ($throwException && empty($admin)) $this->throwException(UserConstants::ADMIN_NOT_FOUND);
        return $admin;
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
            $user->roles()->attach($role->id);
        }
    }

    /**
     * 管理员列表
     * @param $params
     * @return array
     */
    public function list($params): array
    {
        // 构建查询
        $query = User::query()->where('is_admin', 1);

        $query->when($params['email'], function ($query, $emailFilter) {
            $query->where('email', $emailFilter);
        })->when($params['is_locked'], function ($query) {
            $query->where('is_locked', 1);
        });

        $query->orderBy('id', 'desc');
        $filterColumns = ['id', 'name', 'email', 'email_verified_at', 'is_locked', 'is_banned', 'login_at', 'login_ip', 'password_modified_at'];
        $data = static::generatePaginationData($query, $params['per_page'], $params['page'], $filterColumns, false);
        // 处理分页之后的数据
        $data->getCollection()->transform(function ($user) {
            $user->is_email_verified = is_null($user->email_verified_at) ? 0 : 1;
            unset($user->email_verified_at);
            return $user;
        });

        return static::prettyPaginationData($data);
    }

    /**
     * 创建管理员
     * @param array $params
     * @return User
     */
    public function createAdmin(array $params): User
    {
        $params['is_admin'] = 1;
        $params['email_verified_at'] = now();
        $user = User::create($params);
        $this->assignRole($user, 'admin');
        return $user;
    }

    /**
     * 根据id查找用户
     * @param int $id
     * @param bool $throwException
     * @return User|null
     * @throws CommonException
     */
    public function findUserById(int $id, bool $throwException = true): ?User
    {
        $user = User::where('id', $id)->first();
        // 用户未找到
        if ($throwException && empty($user)) $this->throwException(UserConstants::USER_NOT_EXIST);
        // 用户邮箱未验证
        if ($throwException && $this->isEmailNotVerified($user)) $this->throwException(UserConstants::USER_EMAIL_NOT_VERIFY);
        return $user;
    }

    /**
     * 分配角色
     * @throws CommonException
     */
    public function assignRoleByIds($user, $roleIds): void
    {
        // 验证roleIds是否存在
        $existingRoleIds = Role::whereIn('id', $roleIds)->pluck('id')->toArray();
        $nonExistentRoleIds = array_diff($roleIds, $existingRoleIds);
        if ($nonExistentRoleIds) {
            $this->throwException(
                UserConstants::ASSIGN_ROLES_NOT_EXIST,
                ResponseConstants::getMessageByCode(UserConstants::ASSIGN_ROLES_NOT_EXIST, [
                    'role_ids' => implode(',', $nonExistentRoleIds)
                ])
            );
        }
        // 删除所有角色重新分配
        $user->roles()->sync($roleIds);
        // 更新is_admin字段
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $user->update(['is_admin' => in_array($adminRole->id, $roleIds) ? 1 : 0]);
        }
    }

    /**
     * 分配权限
     * @param $user
     * @param $permissionIds
     * @return void
     * @throws CommonException
     */
    public function assignPermissionsByIds($user, $permissionIds): void
    {
        // 获取所有权限及其父权限
        $allPermissions = Permission::getAllPermissions('parent', 'id');

        // 获取层级顺序权限ID
        $flattenedPermissionIds = PermissionRepository::sortPermissions($allPermissions);

        // 验证传参permissionIds是否存在
        $nonExistentPermissionIds = array_diff($permissionIds, $flattenedPermissionIds);
        if ($nonExistentPermissionIds) {
            $this->throwException(
                UserConstants::ASSIGN_PERMISSIONS_NOT_EXIST,
                ResponseConstants::getMessageByCode(UserConstants::ASSIGN_PERMISSIONS_NOT_EXIST, [
                    'permission_ids' => implode(',', $nonExistentPermissionIds)
                ])
            );
        }

        // 获取用户当前已有的权限ID
        $allCurrentPermissionIds = $this->getUserAllPermissionIds($user);

        // 需要添加的权限ID
        $permissionsToAssign = [];
        // 遍历按层级顺序排列的权限ID
        foreach ($flattenedPermissionIds as $permissionId) {
            if (!in_array($permissionId, $permissionIds)) continue;
            // 如果已经拥有该权限，跳过
            if (in_array($permissionId, $allCurrentPermissionIds)) continue;
            // 获取当前权限
            $permission = $allPermissions->get($permissionId);
            // 检查父权限是否存在，并且角色是否已经拥有父权限
            $parentId = $permission->parent_id;
            if ($parentId && !in_array($parentId, $allCurrentPermissionIds)) {
                // 父权限不存在或角色没有父权限，抛出异常或返回错误信息
                $this->throwException(
                    RolePermissionConstants::PARENT_PERMISSION_NOT_EXIST,
                    ResponseConstants::getMessageByCode(
                        RolePermissionConstants::PARENT_PERMISSION_NOT_EXIST,
                        ['parent_id' => $parentId]
                    ));
            }
            $allCurrentPermissionIds[] = $permissionId;
            $permissionsToAssign[] = $permissionId;
        }
        // 批量添加权限
        if (!empty($permissionsToAssign)) {
            $user->permissions()->attach($permissionsToAssign);
        }
    }

    /**
     * 撤销权限
     * @param $user
     * @param $permissionIds
     * @return void
     * @throws CommonException
     */
    public function revokePermissionsByIds($user, $permissionIds): void
    {
        // 获取当前已有的权限ID
        $currentPermissionIds = $user->permissions()->pluck('id')->toArray();
        $notFoundPermissionIds = array_diff($permissionIds, $currentPermissionIds);
        if ($notFoundPermissionIds) {
            $this->throwException(
                RolePermissionConstants::PERMISSION_IDS_NOT_FOUND,
                ResponseConstants::getMessageByCode(
                    RolePermissionConstants::PERMISSION_IDS_NOT_FOUND,
                    ['permission_ids' => implode(',', $notFoundPermissionIds)]
                ));
        }
        // 撤销权限如果有子权限一并撤销
        $allPermissions = Permission::getAllPermissions('children', 'id');
        $childrenIds = [];
        foreach ($permissionIds as $permissionId) {
            $permission = $allPermissions->get($permissionId);
            //找出所有子权限
            PermissionRepository::getAllChildrenIds($permission, $childrenIds);
        }
        $user->permissions()->detach($childrenIds);
    }

    /**
     * 获取用户当前已有的权限ID
     * @param $user
     * @return array
     */
    private function getUserAllPermissionIds($user): array
    {
        // 提取当前用户的所有角色的权限ID
        $currentRolesPermissionIds = $user->roles->map(function ($role) {
            return $role->permissions->pluck('id');
        })->flatten()->toArray();

        // 获取当前用户已有权限ID
        $currentPermissionIds = $user->permissions()->pluck('id')->toArray();
        // 合并所有权限ID
        return array_unique(array_merge($currentRolesPermissionIds, $currentPermissionIds));
    }

}
