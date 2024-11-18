<?php

namespace App\Http\Controllers\Api\Log;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginLogPageRequest;
use App\Http\Services\AdminService;
use App\Http\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;


class LoginLogController extends Controller
{
    use HttpResponses;

    protected AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * 登录日志列表
     * @param LoginLogPageRequest $request
     * @return JsonResponse
     */
    public function index(LoginLogPageRequest $request): JsonResponse
    {
        $list = $this->adminService->loginLogs($request);
        return $this->success($list);
    }

}
