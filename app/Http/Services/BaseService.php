<?php

namespace App\Http\Services;

use App\Constants\CommonConstants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

abstract class BaseService
{
    protected DB $db;
    protected Log $log;

    public function __construct()
    {
        $this->db = app(DB::class);
        $this->log = app(Log::class);
    }

    /**
     * 初始化分页请求参数
     * @param Request $request
     * @return void
     */
    protected function initPageRequest(Request $request): void
    {
        // 设置默认请求参数
        $this->setDefaultRequestParams($request, [
            'page' => 1,
            'per_page' => CommonConstants::PAGE_SIZE
        ]);
    }

    protected function setDefaultRequestParams(Request $request, array $defaults)
    {
        foreach ($defaults as $key => $value) {
            if (!$request->has($key)) {
                $request->merge([$key => $value]);
            }
        }
    }

    /**
     * 根据值去查找国家化的键名
     * @param $value
     * @param string $filename
     * @return false|int|string|null
     */
    public static function findTranslationKey($value, string $filename = 'roles')
    {
        // 获取当前语言
        $locale = app()->getLocale();

        // 获取语言文件路径
        $langPath = resource_path("lang/{$locale}/{$filename}.php");

        // 检查文件是否存在
        if (!File::exists($langPath)) {
            return 'none';
        }

        // 加载语言文件
        $translations = require $langPath;

        // 查找对应的键名
        $key = array_search($value, $translations);

        return $key ?: 'none';
    }

}
