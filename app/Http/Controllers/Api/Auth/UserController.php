<?php

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\CommonException;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ModifyNameRequest;
use App\Http\Requests\User\ModifyPasswordRequest;
use App\Http\Services\UserService;
use App\Http\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;


class UserController extends Controller
{
    use HttpResponses;

    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * 获取用户详情
     * @return JsonResponse
     */
    public function details(): JsonResponse
    {
        $userData = $this->userService->details();
        return $this->success($userData);
    }

    /**
     * 修改用户名
     * @param ModifyNameRequest $request
     * @return JsonResponse
     */
    public function modifyName(ModifyNameRequest $request): JsonResponse
    {
        $this->userService->modifyName($request);
        return $this->successNoData('MODIFY_NAME_SUCCESS');
    }

    /**
     * 修改密码
     * @param ModifyPasswordRequest $request
     * @return JsonResponse
     * @throws CommonException
     */
    public function modifyPassword(ModifyPasswordRequest $request): JsonResponse
    {
        $this->userService->modifyPassword($request);
        return $this->successNoData('MODIFY_PASSWORD_SUCCESS');
    }

    /**
     * 查看自己的权限
     * @return JsonResponse
     */
    public function viewPermission(): JsonResponse
    {
        $data = $this->userService->viewPermission();
        return $this->success($data);
    }
}
