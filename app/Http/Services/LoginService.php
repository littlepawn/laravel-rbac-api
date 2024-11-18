<?php

namespace App\Http\Services;

use App\Constants\CommonConstants;
use App\Constants\response\UserConstants;
use App\Constants\ResponseConstants;
use App\Events\UserLoginEvent;
use App\Exceptions\CommonException;
use App\Http\Repositories\Interface\UserRepositoryInterface;
use App\Http\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginService extends BaseService
{
    use HttpResponses;

    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
    }

    /**
     * 登录逻辑
     * @param Request $request
     * @return string
     * @throws CommonException
     */
    public function login(Request $request)
    {
        $user = $this->userRepository->findByEmail($request->email);

        // 账号被禁用
        if ($this->userRepository->isBanned($user)) $this->throwException(UserConstants::USER_BANNED);
        // 账号被锁定
        if ($this->userRepository->isLocked($user)) $this->throwException(UserConstants::USER_LOCKED);
        // 密码错误
        if ($this->userRepository->isPasswordIncorrect($request->password, $user)) $this->handlePasswordRetry($user);
        // 邮箱未认证
        if ($this->userRepository->isEmailNotVerified($user)) $this->throwException(UserConstants::USER_EMAIL_NOT_VERIFY);

        return $this->generateTokenAndResetErrorCount($user);
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        $user = auth()->user();
        $user->tokens()->delete();
    }

    /**
     * 处理密码错误重试
     * @param $user
     * @return JsonResponse
     * @throws CommonException
     */
    private function handlePasswordRetry($user): JsonResponse
    {
        $user->increment('login_error_count');
        $loginErrorCount = $user->login_error_count;

        if ($loginErrorCount >= CommonConstants::PASSWORD_MAX_RETRY_TIMES) {
            $user->update(['is_locked' => 1]);
            $this->throwException(UserConstants::USER_PASSWORD_RETRY_MAX, ResponseConstants::getMessageByCode(UserConstants::USER_PASSWORD_RETRY_MAX, ['total_err_count' => CommonConstants::PASSWORD_MAX_RETRY_TIMES]));
        }

        // 密码错误，并附上错误次数
        $this->throwException(UserConstants::USER_LOGIN_PASSWORD_NO_MATCH, ResponseConstants::getMessageByCode(UserConstants::USER_LOGIN_PASSWORD_NO_MATCH, ['err_count' => $loginErrorCount, 'total_err_count' => CommonConstants::PASSWORD_MAX_RETRY_TIMES]));
    }

    /**
     * 生成token并重置错误次数
     * @param $user
     * @return string
     */
    private function generateTokenAndResetErrorCount($user): string
    {
        $user->tokens()->delete();

        $expiresAt = now()->addMinutes($this->userRepository->getTokenExpireIn());
        $token = $user->createToken(config('app.name'), [], $expiresAt)->plainTextToken;
        $login_at = now();
        $login_ip = request()->ip();
        $user->login_ip = $login_ip;
        $user->login_at = $login_at;
        // 登录错误次数清零
        $user->login_error_count = 0;
        $user->save();

        $loginInfo = [
            'user' => $user,
            'login_at' => $login_at,
            'ip' => $login_ip,
            'device' => request()->userAgent(),
        ];

        // 记录登录日志
        event(new UserLoginEvent($loginInfo));

        return $token;
    }
}
