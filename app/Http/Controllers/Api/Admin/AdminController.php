<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\CommonException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignPermissionRequest;
use App\Http\Requests\Admin\AssignRoleRequest;
use App\Http\Requests\Admin\DeleteRequest;
use App\Http\Requests\Admin\DetailRequest;
use App\Http\Requests\Admin\InviteConfirmRequest;
use App\Http\Requests\Admin\InviteRequest;
use App\Http\Requests\Admin\ListPageRequest;
use App\Http\Requests\Admin\ModifyRequest;
use App\Http\Requests\Admin\RevokePermissionRequest;
use App\Http\Services\AdminService;
use App\Http\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;


class AdminController extends Controller
{
    use HttpResponses;

    protected AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * 管理员列表
     * @param ListPageRequest $request
     * @return JsonResponse
     */
    public function list(ListPageRequest $request): JsonResponse
    {
        $list = $this->adminService->list($request);
        return $this->success($list);
    }

    /**
     * 管理员详情
     * @param DetailRequest $request
     * @return JsonResponse
     */
    public function details(DetailRequest $request): JsonResponse
    {
        $details = $this->adminService->details($request);
        return $this->success($details);
    }

    /**
     * 修改管理员信息
     * @param ModifyRequest $request
     * @return JsonResponse
     * @throws CommonException
     */
    public function modify(ModifyRequest $request): JsonResponse
    {
        $this->adminService->modify($request);
        return $this->successNoData('MODIFY_SUCCESS');
    }

    /**
     * 删除管理员
     * @param DeleteRequest $request
     * @return JsonResponse
     * @throws CommonException
     */
    public function delete(DeleteRequest $request): JsonResponse
    {
        $this->adminService->delete($request);
        return $this->successNoData('DELETE_SUCCESS');
    }

    /**
     * 邀请注册管理员
     * @param InviteRequest $request
     * @return JsonResponse
     */
    public function invite(InviteRequest $request): JsonResponse
    {
        $this->adminService->invite($request);
        return $this->successNoData('INVITE_SUCCESS');
    }

    /**
     * 确认邀请注册
     * @param InviteConfirmRequest $request
     * @return JsonResponse
     * @throws CommonException
     */
    public function inviteConfirm(InviteConfirmRequest $request): JsonResponse
    {
        $this->adminService->inviteConfirm($request);
        return $this->success();
    }

    /**
     * 给用户分配角色
     * @param AssignRoleRequest $request
     * @return JsonResponse
     * @throws CommonException
     */
    public function assignRole(AssignRoleRequest $request): JsonResponse
    {
        $this->adminService->assignRole($request);
        return $this->success();
    }

    /**
     * 给用户分配权限
     * @param AssignPermissionRequest $request
     * @return JsonResponse
     * @throws CommonException
     */
    public function assignPermission(AssignPermissionRequest $request): JsonResponse
    {
        $this->adminService->assignPermission($request);
        return $this->success();
    }

    /**
     * 撤销用户的权限
     * @param RevokePermissionRequest $request
     * @return JsonResponse
     * @throws CommonException
     */
    public function revokePermission(RevokePermissionRequest $request): JsonResponse
    {
        $this->adminService->revokePermission($request);
        return $this->success();
    }
}
