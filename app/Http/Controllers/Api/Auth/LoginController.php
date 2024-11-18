<?php

namespace App\Http\Controllers\API\Auth;

use App\Exceptions\CommonException;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginRequest;
use App\Http\Services\LoginService;
use App\Http\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;


class LoginController extends Controller
{
    use HttpResponses;

    protected LoginService $loginService;

    public function __construct(LoginService $loginLogService)
    {
        $this->loginService = $loginLogService;
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws CommonException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->loginService->login($request);
        return $this->success(['token' => $token], 'LOGIN_SUCCESS');
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $this->loginService->logout();
        return $this->successNoData('LOGIN_OUT_SUCCESS');
    }
}
