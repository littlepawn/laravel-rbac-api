<?php

namespace App\Http\Repositories;

use App\Constants\response\RolePermissionConstants;
use App\Constants\ResponseConstants;
use App\Exceptions\CommonException;
use App\Http\Traits\CommonRepository;
use App\Http\Traits\HttpResponses;
use App\Models\User\Permission;
use App\Models\User\Role;

class RoleRepository
{
    use HttpResponses, CommonRepository;

    /**
     * 角色列表
     * @param array $params
     * @return array
     */
    public function list(array $params): array
    {
        $slugName = $params['slug'] ?? null;

        $query = Role::query()->withoutTrashed();
        $query->when($slugName, function ($query, $slugName) {
            $query->where('slug', $slugName);
        });
        $query->orderBy('id', 'desc');
        $data = static::generatePaginationData($query, $params['per_page'], $params['page'], ['id', 'slug'], false);
        $items = $data->items();

        // 遍历每个项并增加 name 字段
        foreach ($items as &$item) {
            $item['name'] = $item->name ?? '';
        }
        return static::prettyPaginationData($data);
    }

    /**
     * 根据id获取角色详情
     * @param $id
     * @param bool $throwException
     * @return mixed
     * @throws CommonException
     */
    public function findById($id, bool $throwException = true): mixed
    {
        $role = Role::find($id);
        if ($throwException && empty($role)) $this->throwException(RolePermissionConstants::ROLE_NOT_EXIST);
        return $role;
    }

    /**
     * 根据slug获取角色详情
     * @param $slug
     * @return mixed
     */
    public function findBySlug($slug): mixed
    {
        return Role::where('slug', $slug)->withoutTrashed()->first();
    }

    /**
     * 新增角色
     * @param array $params
     * @return void
     */
    public function create(array $params): void
    {
        Role::create($params);
    }


    /**
     * 是否还有用户关联该角色
     * @param int $roleId
     * @return array
     * @throws CommonException
     */
    public function relatedUser(int $roleId): array
    {
        $role = $this->findById($roleId);
        $relatedUser = $role->users()->exists();
        return [$role, $relatedUser];
    }


    /**
     * 获取角色的权限
     * @param Role $role
     * @return array
     */
    public function getPermissions(Role $role): array
    {
        $permissions = $role->permissions()->get(['id', 'slug'])->makeHidden('pivot');;
        foreach ($permissions as &$permission) {
            $permission['name'] = $permission->name ?? '';
        }
        return $permissions->toArray();
    }

    /**
     * 分配权限
     * @throws CommonException
     */
    public function assignPermission(Role $role, array $permissionIds): void
    {
        // 获取所有权限及其父权限
        $allPermissions = Permission::getAllPermissions('parent', 'id');

        // 获取层级顺序权限ID
        $flattenedPermissionIds = PermissionRepository::sortPermissions($allPermissions);

        // 获取角色当前已有的权限ID
        $currentPermissionIds = $role->permissions()->pluck('id')->toArray();

        $noFoundPermissionIds = array_diff($permissionIds, $flattenedPermissionIds);
        if ($noFoundPermissionIds) {
            $this->throwException(
                RolePermissionConstants::PERMISSION_IDS_NOT_FOUND,
                ResponseConstants::getMessageByCode(
                    RolePermissionConstants::PERMISSION_IDS_NOT_FOUND,
                    ['permission_ids' => implode(',', $noFoundPermissionIds)]
                ));
        }

        // 需要添加的权限ID
        $permissionsToAssign = [];
        // 遍历按层级顺序排列的权限ID
        foreach ($flattenedPermissionIds as $permissionId) {
            if (!in_array($permissionId, $permissionIds)) continue;
            // 如果角色已经拥有该权限，跳过
            if (in_array($permissionId, $currentPermissionIds)) continue;
            // 获取当前权限
            $permission = $allPermissions->get($permissionId);
            // 检查父权限是否存在，并且角色是否已经拥有父权限
            $parentId = $permission->parent_id;
            if ($parentId && !in_array($parentId, $currentPermissionIds)) {
                // 父权限不存在或角色没有父权限，抛出异常或返回错误信息
                $this->throwException(
                    RolePermissionConstants::PARENT_PERMISSION_NOT_EXIST,
                    ResponseConstants::getMessageByCode(
                        RolePermissionConstants::PARENT_PERMISSION_NOT_EXIST,
                        ['parent_id' => $parentId]
                    ));
            }

            $currentPermissionIds[] = $permissionId;
            $permissionsToAssign[] = $permissionId;
        }
        // 批量添加权限
        if (!empty($permissionsToAssign)) {
            $role->permissions()->attach($permissionsToAssign);
        }
    }

    /**
     * 撤销权限
     * @throws CommonException
     */
    public function revokePermission(Role $role, array $permissionIds): void
    {
        // 获取角色当前已有的权限ID
        $currentPermissionIds = $role->permissions()->pluck('id')->toArray();
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
        $role->permissions()->detach($childrenIds);
    }


}
