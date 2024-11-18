<?php

namespace App\Http\Services;

use App\Constants\response\RolePermissionConstants;
use App\Exceptions\CommonException;
use App\Http\Repositories\RoleRepository;
use App\Http\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RoleService extends BaseService
{
    use HttpResponses;

    protected RoleRepository $roleRepository;

    public function __construct(
        RoleRepository $roleRepository,
    )
    {
        parent::__construct();
        $this->roleRepository = $roleRepository;
    }

    /**
     * 角色列表
     * @param Request $request
     * @return array
     */
    public function list(Request $request): array
    {
        $this->initPageRequest($request);
        $name = $request->get("name");
        $slug = $name ? static::findTranslationKey($name) : null;
        $this->setDefaultRequestParams($request, ['slug' => $slug]);
        return $this->roleRepository->list($request->all());
    }

    /**
     * 角色详情
     * @param Request $request
     * @return array
     * @throws CommonException
     */
    public function detail(Request $request): array
    {
        $roleModel = $this->roleRepository->findById($request->id);
        $role = $roleModel->only(['id', 'name', 'slug', 'created_at']);
        $role['created_at'] = Carbon::parse($role['created_at'])->format('Y-m-d H:i:s');
        $role['permissions'] = $this->roleRepository->getPermissions($roleModel);

        return $role;
    }

    /**
     * 新增角色
     * @param Request $request
     * @return void
     * @throws CommonException
     */
    public function create(Request $request): void
    {
        $isExist = $this->roleRepository->findBySlug($request->slug);
        // 角色名或角色标识已存在
        if ($isExist) $this->throwException(RolePermissionConstants::ROLE_EXIST);
        $this->roleRepository->create($request->all());
    }

    /**
     * 更新角色
     * @param Request $request
     * @return void
     * @throws CommonException
     */
    public function update(Request $request): void
    {
        if ($request->slug) {
            $existingRole = $this->roleRepository->findBySlug($request->slug);
            // 角色标识已存在，且不是当前角色
            if ($existingRole && $existingRole->id != $request->id) $this->throwException(RolePermissionConstants::ROLE_EXIST);
        }
        $this->roleRepository->findById($request->id)->update($request->only(['slug', 'remark']));
    }

    /**
     * 删除角色
     * @param Request $request
     * @return void
     * @throws CommonException
     */
    public function delete(Request $request): void
    {
        list($role, $relatedUserFlag) = $this->roleRepository->relatedUser($request->id);
        if ($relatedUserFlag) $this->throwException(RolePermissionConstants::ROLE_RELATED_USER);
        $role->delete();
    }

    /**
     * 分配权限
     * @throws CommonException
     */
    public function assignPermission(Request $request): void
    {
        $role = $this->roleRepository->findById($request->id);
        $this->db::transaction(function () use ($role, $request) {
            $this->roleRepository->assignPermission($role, $request->permission_ids);
        });
    }

    /**
     * 撤销权限
     * @throws CommonException
     */
    public function revokePermission(Request $request): void
    {
        $role = $this->roleRepository->findById($request->id);
        $this->db::transaction(function () use ($role, $request) {
            $this->roleRepository->revokePermission($role, $request->permission_ids);
        });
    }


}
