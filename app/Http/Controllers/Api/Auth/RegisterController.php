<?php

namespace App\Http\Controllers\API\Auth;

use App\Constants\ResponseConstants;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\RegisterRequest;
use App\Http\Services\RegisterService;
use App\Http\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    use HttpResponses;

    protected RegisterService $registerService;

    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

    /**
     * @param RegisterRequest $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $this->registerService->register($request);
        return $this->successNoData('REGISTER_SUCCESS');
    }

}
