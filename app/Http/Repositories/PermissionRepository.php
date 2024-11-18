<?php

namespace App\Http\Repositories;

use App\Constants\response\RolePermissionConstants;
use App\Exceptions\CommonException;
use App\Http\Traits\HttpResponses;
use App\Models\User\Permission;
use Illuminate\Database\Eloquent\Collection;

class PermissionRepository
{
    use HttpResponses;

    /**
     * 根据权限id获取权限
     * @param int $id
     * @param bool $throwException
     * @return Permission|null
     * @throws CommonException
     */
    public function findById(int $id, bool $throwException = true): Permission|null
    {
        $permission = Permission::find($id);
        // 用户未找到
        if ($throwException && empty($permission)) $this->throwException(RolePermissionConstants::PERMISSION_NOT_FOUND);
        return $permission;
    }

    /**
     * 根据权限id获取父权限
     * @param int $id
     * @param bool $throwException
     * @return Permission|null
     * @throws CommonException
     */
    public function findParentById(int $id, bool $throwException = true): Permission|null
    {
        $parentPermission = $this->findById($id, false);
        if ($throwException && empty($parentPermission)) $this->throwException(RolePermissionConstants::PARENT_PERMISSION_NOT_FOUND);
        return $parentPermission;
    }

    /**
     * 获取权限树
     * @param array $params
     * @return array
     * @throws CommonException
     */
    public function tree(array $params): array
    {
        $parentId = 0;
        if ($params['slug']) {
            $permission = $this->findBySlug($params['slug']);
            if (!$permission) {
                $this->throwException(RolePermissionConstants::PARENT_PERMISSION_NOT_FOUND);
            }
            $parentId = $permission->id;
        }

        return Permission::getPermissionTree($parentId);
    }

    /**
     * 根据slug获取权限详情
     * @param $slug
     * @return mixed
     */
    public function findBySlug($slug): mixed
    {
        return Permission::where('slug', $slug)->withoutTrashed()->first();
    }

    /**
     * 新增权限
     * @param array $params
     * @return void
     */
    public function create(array $params): void
    {
        Permission::create($params);
    }

    /**
     * 是否还有用户关联该权限
     * @param Permission $permission
     * @return bool
     */
    public function relatedUser(Permission $permission): bool
    {
        return $permission->users()->exists();
    }

    /**
     * 是否还有角色关联该权限
     * @param Permission $permission
     * @return bool
     */
    public function relatedRole(Permission $permission): bool
    {
        return $permission->roles()->exists();
    }

    /**
     * 是否有相关授权
     * @param Permission $permission
     * @return bool
     */
    public function isRelated(Permission $permission)
    {
        if ($this->relatedUser($permission)) {
            return true;
        }
        if ($this->relatedRole($permission)) {
            return true;
        }
        return false;
    }

    /**
     * 是否有子权限
     * @param Permission $permission
     * @return mixed
     */
    public function hasChildren(Permission $permission)
    {
        return Permission::where('parent_id', $permission->id)->exists();
    }

    /**
     * 获取权限的所有子权限ID
     * @param $permission
     * @param array $childrenIds
     * @return void
     */
    public static function getAllChildrenIds($permission, array &$childrenIds): void
    {
        $childrenIds[] = $permission->id;
        $children = $permission->children;
        if ($children) {
            foreach ($children as $child) {
                self::getAllChildrenIds($child, $childrenIds);
            }
        }
    }

    /**
     * 按层级权限排序
     * @param Collection $permissions
     * @return array
     */
    public static function sortPermissions(Collection $permissions)
    {
        $permissionTree = self::buildHierarchy($permissions);
        $flattenedIds = [];
        self::flattenTreeIds($permissionTree, $flattenedIds);
        return $flattenedIds;
    }

    /**
     * 构建权限树
     * @param $permissions
     * @param int $parentId
     * @return array
     */
    private static function buildHierarchy($permissions, int $parentId = 0): array
    {
        $tree = [];
        foreach ($permissions as $permission) {
            if ($permission->parent_id == $parentId) {
                $children = self::buildHierarchy($permissions, $permission->id);
                if ($children) {
                    $permission->children = $children;
                }
                $tree[] = $permission;
            }
        }
        return $tree;
    }

    /**
     * 将权限树转换为按层级顺序排列的权限ID列表
     * @param $tree
     * @param array $flattenedIds
     * @return void
     */
    private static function flattenTreeIds($tree, array &$flattenedIds): void
    {
        foreach ($tree as $node) {
            $flattenedIds[] = $node->id;
            if (isset($node->children)) {
                self::flattenTreeIds($node->children, $flattenedIds);
            }
        }
    }

}
