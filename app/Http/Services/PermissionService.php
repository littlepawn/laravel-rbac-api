<?php

namespace App\Http\Services;

use App\Constants\response\RolePermissionConstants;
use App\Exceptions\CommonException;
use App\Http\Repositories\PermissionRepository;
use App\Http\Traits\HttpResponses;
use Illuminate\Http\Request;

class PermissionService extends BaseService
{
    use HttpResponses;

    protected PermissionRepository $permissionRepository;

    public function __construct(
        PermissionRepository $permissionRepository,
    )
    {
        parent::__construct();
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * 获取权限树
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\CommonException
     */
    public function tree(Request $request): array
    {
//        $name = $request->get("slug");
//        $slug = $name ? static::findTranslationKey($name, 'permissions') : null;
        $slug = $request->get("slug");
        $this->setDefaultRequestParams($request, ['slug' => $slug]);
        return $this->permissionRepository->tree($request->all());
    }

    /**
     * 权限详情
     * @param Request $request
     * @return array
     * @throws CommonException
     */
    public function detail(Request $request): array
    {
        $permissionModel = $this->permissionRepository->findById($request->id);
        return $permissionModel->only(['id', 'name', 'slug', 'uri', 'created_at', 'parent_id',]);
//        return $permissionModel->toArray();
    }

    /**
     * 新增权限
     * @param Request $request
     * @return void
     * @throws CommonException
     */
    public function create(Request $request): void
    {
        $isExist = $this->permissionRepository->findBySlug($request->slug);
        // 权限标识已存在
        if ($isExist) $this->throwException(RolePermissionConstants::PERMISSION_EXIST);

        // parent_id存在时，判断父级权限是否存在; parent_id不存在默认使用1
        $parent_id = $request->get('parent_id', 1);
        $this->setDefaultRequestParams($request, ['parent_id' => $parent_id]);
        $this->permissionRepository->findParentById($request->parent_id);
        $this->permissionRepository->create($request->all());
    }

    /**
     * 更新权限
     * @param Request $request
     * @return void
     * @throws CommonException
     */
    public function update(Request $request): void
    {
        $permissionModel = $this->permissionRepository->findById($request->id);
        // 验证slug
        if ($request->slug) {
            $existingPermission = $this->permissionRepository->findBySlug($request->slug);
            // 权限标识已存在，且不是当前权限
            if ($existingPermission && $existingPermission->id != $request->id) $this->throwException(RolePermissionConstants::PERMISSION_EXIST);
        }
        // 验证parent_id
        if ($request->parent_id && $request->parent_id != $permissionModel->parent_id) {
            $this->permissionRepository->findParentById($request->parent_id);
            if ($this->permissionRepository->isRelated($permissionModel)) {
                $this->throwException(RolePermissionConstants::PERMISSION_RELATED_UPDATE);
            }
        }
        $permissionModel->update($request->all());
    }

    /**
     * 删除权限
     * @throws CommonException
     */
    public function delete(Request $request): void
    {
        $permissionModel = $this->permissionRepository->findById($request->id);
        if ($this->permissionRepository->isRelated($permissionModel)) {
            $this->throwException(RolePermissionConstants::PERMISSION_RELATED_DELETE);
        }
        if ($this->permissionRepository->hasChildren($permissionModel)) {
            $this->throwException(RolePermissionConstants::PERMISSION_HAS_CHILDREN);
        }
        $permissionModel->delete();
    }


}
