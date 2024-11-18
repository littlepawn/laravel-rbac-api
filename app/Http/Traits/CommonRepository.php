<?php

namespace App\Http\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait CommonRepository
{

    /**
     * 构建分页响应数据
     * @param Builder $query
     * @param $perPage
     * @param $page
     * @param string[] $column
     * @param bool $pretty
     * @return array|LengthAwarePaginator
     */
    public static function generatePaginationData(Builder $query, $perPage, $page, $column = ['*'], bool $pretty = true): LengthAwarePaginator|array
    {
        // 分页查询
        $data = $query->paginate($perPage, $column, 'page', $page);
        if ($pretty) $data = self::prettyPaginationData($data);
        return $data;
    }

    /**
     * 美化分页响应数据
     * @param $data
     * @return array
     */
    public static function prettyPaginationData($data): array
    {
        return [
            'meta' => [
                'total' => $data->total(),
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'last_page' => $data->lastPage(),
            ],
            'data' => $data->items(),
        ];
    }

    /**
     * 定义一个函数来捕获闭包内的 SQL 语句
     **/
    public static function captureQueries(callable $callback)
    {
        DB::enableQueryLog();
        $callback();
        $queries = DB::getQueryLog();
        DB::disableQueryLog();
        dd($queries);
    }
}
