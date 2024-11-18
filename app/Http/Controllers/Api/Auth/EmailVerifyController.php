<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\EmailVerifyRequest;
use App\Http\Services\UserService;
use App\Http\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;

class EmailVerifyController extends Controller
{
    use HttpResponses;

    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param EmailVerifyRequest $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function __invoke(EmailVerifyRequest $request): JsonResponse
    {
        $this->userService->verifyEmail($request);
        return $this->successNoData('EMAIL_VERIFY_SUCCESS');
    }

}
