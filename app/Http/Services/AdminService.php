<?php

namespace App\Http\Services;

use App\Constants\RedisConstants;
use App\Constants\response\UserConstants;
use App\Events\InviteEmailEvent;
use App\Exceptions\CommonException;
use App\Http\Repositories\Interface\UserRepositoryInterface;
use App\Http\Repositories\LogRepository;
use App\Http\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;

class AdminService extends BaseService
{
    use HttpResponses;

    protected UserRepositoryInterface $adminRepository;
    protected LogRepository $logRepository;

    public function __construct(
        UserRepositoryInterface $adminRepository,
        LogRepository           $logRepository,
    )
    {
        parent::__construct();
        $this->adminRepository = $adminRepository;
        $this->logRepository = $logRepository;
    }


    /**
     * 登录日志列表
     * @param Request $request
     * @return array
     */
    public function list(Request $request): array
    {
        // 获取请求参数
        $this->initPageRequest($request);
        $this->setDefaultRequestParams($request, [
            'email' => null,
            'is_locked' => null,
        ]);

        return $this->adminRepository->list($request->all());
    }

    /**
     * 管理员详情
     * @param Request $request
     * @return array
     */
    public function details(Request $request): array
    {
        $admin = $this->adminRepository->findById($request->admin_id);
        $adminData = $admin->only(['id', 'name', 'email', 'is_locked', 'is_banned', 'login_at', 'login_ip', 'email_verified_at']);
        // 处理 email_verified_at 字段
        $adminData['email_verified_at'] = Carbon::parse($adminData['email_verified_at'])->format('Y-m-d H:i:s');
        $adminData['is_email_verified'] = is_null($adminData['email_verified_at']) ? 0 : 1;
        return $adminData;
    }

    /**
     * 修改管理员信息
     * @param Request $request
     * @return void
     * @throws CommonException
     */
    public function modify(Request $request): void
    {
        $admin = $this->adminRepository->findById($request->admin_id);
        $name = $request->get('name');
        $is_banned = $request->get('is_banned');
        $currentAdmin = auth()->user();
        if ($is_banned === 1 && $currentAdmin->id === $admin->id) {
            $this->throwException(UserConstants::ADMIN_CANNOT_BAN_SELF);
        }
        $updateData = array_filter(compact('name', 'is_banned'), function ($value) {
            return !is_null($value);
        });
        $admin->update($updateData);
        // 如果状态被修改为封禁，则强制删除登录态
        if ($is_banned) $admin->tokens()->delete();
    }

    /**
     * 删除管理员信息
     * @param Request $request
     * @return void
     * @throws CommonException
     */
    public function delete(Request $request): void
    {
        $admin = $this->adminRepository->findById($request->admin_id);
        $currentAdmin = auth()->user();
        if ($currentAdmin->id === $admin->id) $this->throwException(UserConstants::ADMIN_CANNOT_DELETE_SELF);
        // 删除管理员角色关联
        $this->db::transaction(function () use ($admin) {
            $admin->update(['is_admin' => 0]);
            $admin->roles()->detach();
        });
        // 强制删除登录态
        $admin->tokens()->delete();
    }

    /**
     * 邀请注册管理员
     * @param Request $request
     * @return void
     */
    public function invite(Request $request): void
    {
        // 触发发送邀请邮件事件
        event(new InviteEmailEvent($request->email));
    }

    /**
     * 确认邀请注册
     * @param Request $request
     * @return void
     * @throws CommonException
     */
    public function inviteConfirm(Request $request): void
    {
        $email = $request->get('email');
        $token = $request->get('token');

        $this->setDefaultRequestParams($request, [
            'email' => $email,
            'name' => $request->get('name'),
            'password' => $request->get('password'),
        ]);

        // 验证邀请链接token
        $redisKey = RedisConstants::geneRedisKey(RedisConstants::EMAIL_INVITE_TOKEN_PREFIX, $email);
        $this->verifyToken($redisKey, $token);
        $this->db::transaction(function () use ($request, $redisKey) {
            // 创建管理员
            $this->adminRepository->createAdmin($request->all());
            // 删除redis中的token
            Redis::del($redisKey);
        });
    }

    /**
     * 登录日志列表
     * @param Request $request
     * @return array
     */
    public function loginLogs(Request $request): array
    {
        // 获取请求参数
        $this->initPageRequest($request);
        $this->setDefaultRequestParams($request, [
            'role_keywords' => null,
            'user_id' => null,
            'device' => null,
            'ip' => null,
        ]);

        return $this->logRepository->loginLogs($request->all());
    }

    /**
     * 操作日志列表
     * @param Request $request
     * @return array
     */
    public function operationLogs(Request $request): array
    {
        // 获取请求参数
        $this->initPageRequest($request);
        $this->setDefaultRequestParams($request, [
            'role_keywords' => null,
            'user_id' => null,
            'method' => null,
            'ip' => null,
        ]);

        return $this->logRepository->operationLogs($request->all());
    }

    /**
     * 分配角色
     * @param Request $request
     * @throws CommonException
     */
    public function assignRole(Request $request): void
    {
        $user = $this->adminRepository->findUserById($request->user_id);
        if (auth()->user()->id === $user->id) $this->throwException(UserConstants::ADMIN_CANNOT_ASSIGN_SELF);
        $this->db::transaction(function () use ($user, $request) {
            $this->adminRepository->assignRoleByIds($user, $request->get('role_ids'));
        });
    }

    /**
     * 分配权限
     * @param Request $request
     * @return void
     * @throws CommonException
     */
    public function assignPermission(Request $request): void
    {
        $user = $this->adminRepository->findUserById($request->user_id);
        if (auth()->user()->id === $user->id) $this->throwException(UserConstants::ADMIN_CANNOT_ASSIGN_SELF);
        $this->db::transaction(function () use ($user, $request) {
            $this->adminRepository->assignPermissionsByIds($user, $request->get('permission_ids'));
        });
    }

    /**
     * 撤销用户的权限
     * @param Request $request
     * @return void
     * @throws CommonException
     */
    public function revokePermission(Request $request): void
    {
        $user = $this->adminRepository->findUserById($request->user_id);
        if (auth()->user()->id === $user->id) $this->throwException(UserConstants::ADMIN_CANNOT_REVOKE_SELF);
        $this->db::transaction(function () use ($user, $request) {
            $this->adminRepository->revokePermissionsByIds($user, $request->get('permission_ids'));
        });
    }


    /**
     * 验证邀请链接token
     * @param string $redisKey
     * @param string $token
     * @return void
     * @throws CommonException
     */
    private function verifyToken(string $redisKey, string $token): void
    {
        // 获取redis中的token
        $inviteToken = Redis::get($redisKey);

        // 验证token
        if (empty($inviteToken)) $this->throwException(UserConstants::ADMIN_EMAIL_INVITE_URL_EXPIRED);
        if ($inviteToken !== $token) $this->throwException(UserConstants::ADMIN_EMAIL_INVITE_FAILED);
    }
}
