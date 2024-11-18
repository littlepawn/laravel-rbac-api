<?php

namespace App\Http\Services;

use App\Events\UserRegisteredEvent;
use App\Http\Repositories\Interface\UserRepositoryInterface;
use App\Http\Traits\HttpResponses;
use Illuminate\Http\Request;

class RegisterService extends BaseService
{
    use HttpResponses;

    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
    }

    /**
     * 注册逻辑
     * @param Request $request
     * @return void
     * @throws \Throwable
     */
    public function register(Request $request): void
    {
        $request->password = bcrypt($request->password);

        try {
            $user = $this->db::transaction(function () use ($request) {
                $user = $this->userRepository->create($request->all());
                //分配默认角色
                $this->userRepository->assignRole($user, 'user');
                return $user;
            });
            // 注册流程完成发送邮件
            event(new UserRegisteredEvent($user));
        } catch (\Throwable $e) {
            $this->log::error($e->getMessage());
            throw $e;
        }
    }
}
