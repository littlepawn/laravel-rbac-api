<?php

namespace App\Http\Controllers\Api\RolePermission;

use App\Exceptions\CommonException;
use App\Http\Controllers\Controller;
use App\Http\Requests\RolePermission\PermissionCreateRequest;
use App\Http\Requests\RolePermission\PermissionDeleteRequest;
use App\Http\Requests\RolePermission\PermissionDetailRequest;
use App\Http\Requests\RolePermission\PermissionTreeRequest;
use App\Http\Requests\RolePermission\PermissionUpdateRequest;
use App\Http\Services\PermissionService;
use App\Http\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    use HttpResponses;

    protected PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * 权限树
     * @param PermissionTreeRequest $request
     * @return JsonResponse
     * @throws CommonException
     */
    public function tree(PermissionTreeRequest $request): JsonResponse
    {
        $tree = $this->permissionService->tree($request);
        return $this->success($tree);
    }

    /**
     * 权限详情
     * @param PermissionDetailRequest $request
     * @return JsonResponse
     * @throws CommonException
     */
    public function details(PermissionDetailRequest $request): JsonResponse
    {
        $detail = $this->permissionService->detail($request);
        return $this->success($detail);
    }

    /**
     * 新增权限
     * @param PermissionCreateRequest $request
     * @return JsonResponse
     * @throws CommonException
     */
    public function create(PermissionCreateRequest $request): JsonResponse
    {
        $this->permissionService->create($request);
        return $this->success();
    }

    /**
     * 更新权限
     * @param PermissionUpdateRequest $request
     * @return JsonResponse
     * @throws CommonException
     */
    public function update(PermissionUpdateRequest $request): JsonResponse
    {
        $this->permissionService->update($request);
        return $this->success();
    }

    /**
     * 删除权限
     * @param PermissionDeleteRequest $request
     * @return JsonResponse
     * @throws CommonException
     */
    public function delete(PermissionDeleteRequest $request): JsonResponse
    {
        $this->permissionService->delete($request);
        return $this->success();
    }
}
