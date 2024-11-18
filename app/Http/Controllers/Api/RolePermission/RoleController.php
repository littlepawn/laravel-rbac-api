<?php

namespace App\Http\Controllers\Api\RolePermission;

use App\Exceptions\CommonException;
use App\Http\Controllers\Controller;
use App\Http\Requests\RolePermission\RoleAssignPermissionRequest;
use App\Http\Requests\RolePermission\RoleCreateRequest;
use App\Http\Requests\RolePermission\RoleDeleteRequest;
use App\Http\Requests\RolePermission\RoleDetailRequest;
use App\Http\Requests\RolePermission\RoleListPageRequest;
use App\Http\Requests\RolePermission\RoleRevokePermissionRequest;
use App\Http\Requests\RolePermission\RoleUpdateRequest;
use App\Http\Services\RoleService;
use App\Http\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    use HttpResponses;

    protected RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * 角色列表
     * @param RoleListPageRequest $request
     * @return JsonResponse
     */
    public function index(RoleListPageRequest $request): JsonResponse
    {
        $list = $this->roleService->list($request);
        return $this->success($list);
    }

    /**
     * 角色详情
     * @param RoleDetailRequest $request
     * @return JsonResponse
     * @throws CommonException
     */
    public function details(RoleDetailRequest $request): JsonResponse
    {
        $detail = $this->roleService->detail($request);
        return $this->success($detail);
    }

    /**
     * 新增角色
     * @param RoleCreateRequest $request
     * @return JsonResponse
     * @throws CommonException
     */
    public function create(RoleCreateRequest $request): JsonResponse
    {
        $this->roleService->create($request);
        return $this->success();
    }

    /**
     * 更新角色
     * @param RoleUpdateRequest $request
     * @return JsonResponse
     * @throws CommonException
     */
    public function update(RoleUpdateRequest $request): JsonResponse
    {
        $this->roleService->update($request);
        return $this->success();
    }

    /**
     * 删除角色
     * @param RoleDeleteRequest $request
     * @return JsonResponse
     * @throws CommonException
     */
    public function delete(RoleDeleteRequest $request): JsonResponse
    {
        $this->roleService->delete($request);
        return $this->success();
    }

    /**
     * 分配权限
     * @param RoleAssignPermissionRequest $request
     * @return JsonResponse
     * @throws CommonException
     */
    public function assignPermission(RoleAssignPermissionRequest $request): JsonResponse
    {
        $this->roleService->assignPermission($request);
        return $this->success();
    }

    /**
     * 撤销权限
     * @param RoleRevokePermissionRequest $request
     * @return JsonResponse
     * @throws CommonException
     */
    public function revokePermission(RoleRevokePermissionRequest $request): JsonResponse
    {
        $this->roleService->revokePermission($request);
        return $this->success();
    }
}
