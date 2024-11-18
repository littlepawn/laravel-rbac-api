<?php

namespace App\Http\Services;

use App\Constants\CommonConstants;
use App\Constants\RedisConstants;
use App\Constants\response\UserConstants;
use App\Exceptions\CommonException;
use App\Http\Repositories\Interface\UserRepositoryInterface;
use App\Http\Traits\HttpResponses;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class UserService extends BaseService
{
    use HttpResponses;

    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
    }

    /**
     * 验证邮箱
     * @param Request $request
     * @return void
     * @throws \Throwable
     */
    public function verifyEmail(Request $request): void
    {
        $user = $this->userRepository->findByEmail($request->email, false);
        if (empty($user)) $this->throwException(UserConstants::USER_EMAIL_NOT_EXIST);

        // 获取redis中的token
        $EMAIL_VERIFY_KEY = RedisConstants::geneRedisKey(RedisConstants::EMAIL_VERIFY_CODE_PREFIX, $request->email);
        $isToken = Redis::get($EMAIL_VERIFY_KEY);

        // 验证token
        if (empty($isToken)) $this->throwException(UserConstants::USER_EMAIL_VERIFY_URL_EXPIRED);
        if ($isToken !== $request->token) $this->throwException(UserConstants::USER_EMAIL_VERIFY_FAILED);

        // 更新用户邮箱验证状态和删除token
//        $user->setAttribute('email_verified_at', now())->save();
        $user->update(['email_verified_at' => now()]);
        Redis::del($EMAIL_VERIFY_KEY);
    }

    /**
     * 忘记密码
     * @param Request $request
     * @return void
     */
    public function forgotPassword(Request $request): void
    {
        $user = $this->userRepository->findByEmail($request->email);
        // 调用系统sanctum的密码重置库
        $token = Password::createToken($user);

        // 模拟发送邮件重置链接为写入
        $this->sendResetPasswordEmail($request->email, $token);

    }

    protected function sendResetPasswordEmail(string $email, string $token): void
    {
        // TODO
        $filename = CommonConstants::PASSWORD_RESET_FILENAME;
        $data = [];
        if (Storage::exists($filename)) {
            $rawData = Storage::get($filename);
            if (!empty($rawData)) $data = json_decode($rawData, true);
        }
        $resetUrl = config('app.dev_config.url') . route('reset.password', ['email' => $email, 'token' => $token], false);

        $data[$email] = [
            'token' => $token,
            'reset_url' => $resetUrl,
        ];
        Storage::put($filename, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * 重置密码
     * @param Request $request
     * @return void
     * @throws CommonException
     */
    public function resetPassword(Request $request): void
    {
        $user = $this->userRepository->findByEmail($request->email);
        // 处理密码重置
        $response = Password::reset(
            $request->only('email', 'password', 'token'),
            function ($user, $password) {
                /**
                 * @var User $user
                 */
                $user->forceFill([
                    'password' => Hash::make($password),
                    'password_modified_at' => now(),
                ])->save();
            }
        );
        switch ($response) {
            case Password::PASSWORD_RESET:
                $user->update(['is_locked' => 0]);
                // 删除已登陆的token
                $user->tokens()->delete();
                break;
            case Password::INVALID_TOKEN:
                $this->throwException(UserConstants::USER_INVALID_RESET_TOKEN);
            case Password::INVALID_USER:
                $this->throwException(UserConstants::USER_NOT_EXIST);
            default:
                $this->throwException(UserConstants::USER_RESET_PASSWORD_FAILED);
        }
    }

    /**
     * 用户详情
     * @return array
     */
    public function details(): array
    {
        $user = auth()->user();
        // 加载关联的角色
        $user->load('roles');

        // 提取部分字段并包含关联的角色
        $userData = $user->only(['id', 'name', 'email']);
        $userData['roles'] = $user->roles->map(function ($role) {
            return $role->only(['name']);
        });
        return $userData;
    }

    /**
     * 修改用户名
     * @param Request $request
     * @return void
     */
    public function modifyName(Request $request): void
    {
        $user = auth()->user();
        $user->update($request->only(['name']));
    }

    /**
     * 修改密码
     * @param Request $request
     * @return void
     * @throws CommonException
     */
    public function modifyPassword(Request $request): void
    {
        $user = auth()->user();
        if (Hash::check($request->password, $user->password)) {
            $this->throwException(UserConstants::USER_PASSWORD_REPEATED);
        }
        $user->password_modified_at = now();
        $user->password = bcrypt($request->password);
        $user->save();
        // 删除已登陆的token
        $user->tokens()->delete();
    }

    /**
     * 查看权限
     * @return mixed
     */
    public function viewPermission()
    {
        $user = auth()->user();
        if ($user->is_admin) {
            $data['role_list'] = ['*'];
        } else {
            $data['role_list'] = $this->userRepository->getUserPermissions($user);
        }
        return $data;
    }
}
