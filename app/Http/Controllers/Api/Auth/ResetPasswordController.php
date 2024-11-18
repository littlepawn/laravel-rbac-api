<?php

namespace App\Http\Controllers\API\Auth;


use App\Exceptions\CommonException;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ApplyResetPasswordRequest;
use App\Http\Requests\User\ResetPasswordRequest;
use App\Http\Services\UserService;
use App\Http\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;

class ResetPasswordController extends Controller
{
    use HttpResponses;

    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * 忘记密码
     * @param ApplyResetPasswordRequest $request
     * @return JsonResponse
     */
    public function forget(ApplyResetPasswordRequest $request): JsonResponse
    {
        $this->userService->forgotPassword($request);
        return $this->success();
    }

    /**
     * 重置密码
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     * @throws CommonException
     */
    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $this->userService->resetPassword($request);
        return $this->successNoData('RESET_PASSWORD_SUCCESS');
    }
}
