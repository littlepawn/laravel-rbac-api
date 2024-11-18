<?php

namespace App\Http\Repositories;

use App\Http\Traits\CommonRepository;
use App\Http\Traits\HttpResponses;
use App\Models\Log\UserLoginLog;
use App\Models\Log\UserOperationLog;

class LogRepository
{
    use HttpResponses, CommonRepository;

    public function loginLogs(array $params): array
    {
        // 构建查询
        $query = UserLoginLog::query();

        $query->when($params['role_keywords'], function ($query) use ($params) {
            $query->whereRaw("POSITION(? IN role_name) > 0", [$params['role_keywords']]);
        })->when($params['user_id'], function ($query) use ($params) {
            $query->where('user_id', $params['user_id']);
        })->when($params['device'], function ($query) use ($params) {
            $query->where('device', $params['device']);
        })->when($params['ip'], function ($query) use ($params) {
            $query->where('ip', $params['ip']);
        });
        $query->orderBy('id', 'desc');
        return static::generatePaginationData($query, $params['per_page'], $params['page']);
    }

    public function operationLogs(array $params): array
    {
        // 构建查询
        $query = UserOperationLog::query();

        $query->when($params['role_keywords'], function ($query) use ($params) {
            $query->whereRaw("POSITION(? IN role_name) > 0", [$params['role_keywords']]);
        })->when($params['user_id'], function ($query) use ($params) {
            $query->where('user_id', $params['user_id']);
        })->when($params['method'], function ($query) use ($params) {
            $query->where('method', strtoupper($params['method']));
        })->when($params['ip'], function ($query) use ($params) {
            $query->where('ip', $params['ip']);
        });
        $query->orderBy('id', 'desc');
        return static::generatePaginationData($query, $params['per_page'], $params['page']);
    }


}
