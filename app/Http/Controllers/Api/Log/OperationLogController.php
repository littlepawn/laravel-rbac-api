<?php

namespace App\Http\Controllers\Api\Log;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OperationLogPageRequest;
use App\Http\Services\AdminService;
use App\Http\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;


class OperationLogController extends Controller
{
    use HttpResponses;

    protected AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * 操作日志列表
     * @param OperationLogPageRequest $request
     * @return JsonResponse
     */
    public function index(OperationLogPageRequest $request): JsonResponse
    {
        $list = $this->adminService->operationLogs($request);
        return $this->success($list);
    }

}
