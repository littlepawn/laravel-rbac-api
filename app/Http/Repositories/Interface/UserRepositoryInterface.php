<?php

namespace App\Http\Repositories\Interface;

use App\Models\User\User;

Interface UserRepositoryInterface
{
    /** 根据邮箱查找用户 **/
    public function findByEmail(string $email): ?User;
    /** 根据id查找用户 **/
    public function findById(int $id): ?User;
    /** 创建用户 **/
    public function create(array $data): User;
    /** 判断密码是否正确 **/
    public function isPasswordIncorrect(string $password, User $user): bool;
    /** 判断邮箱是否未验证 **/
    public function isEmailNotVerified(User $user): bool;
    /** 判断账号是否被锁定 **/
    public function isLocked(User $user): bool;
    /** 判断账号是否被禁用 **/
    public function isBanned(User $user): bool;
    /** 分配角色 **/
    public function assignRole(User $user, string $string);
}
