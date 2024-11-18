<?php

namespace App\Constants;

use App\Http\Traits\ConstantsHelper;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

/**
 * 响应码常量类
 */
final class ResponseConstants
{
    use ConstantsHelper;

    public const SUCCESS = 0;
    public const PARAMS_ERROR = 9001;
    public const SERVER_ERROR = 9101;
    public const NOT_FOUND = 9201;
    public const METHOD_NOT_ALLOWED = 9301;
    public const DATABASE_UNIQUE_CONFLICT = 9401;
    public const UNKNOWN = 9999;

    /**
     * 获取所有状态码和消息的映射数组
     *
     * @return array 状态码和消息的映射数组
     */
    public static function getStatusMessages(): array
    {
        $keyValuePairs = self::getKeyValuePairs();
        $directory = app_path('Constants/response');
        $files = File::allFiles($directory);

        // 遍历所有响应码常量类
        foreach ($files as $file) {
            $className = 'App\\Constants\\response\\' . pathinfo($file, PATHINFO_FILENAME);
            if (class_exists($className)) {
                $reflection = new \ReflectionClass($className);
                if ($reflection->hasMethod('getKeyValuePairs')) {
                    $keyValuePairs += $className::getKeyValuePairs();
                }
            }
        }

        return $keyValuePairs;
    }

    /**
     * 获取特定状态码的消息
     *
     * @param int $code 状态码
     * @param array $params 附加参数
     * @return string|null 状态码对应的消息，如果不存在则返回 null
     */
    public static function getMessageByCode(int $code, array $params = []): ?string
    {
        $statusMessages = self::getStatusMessages();
        $messageKey = $statusMessages[$code] ?? null;

        if ($messageKey) {
            if (Lang::has($messageKey)) return $params ? __($messageKey, $params) : __($messageKey);
            return Str::afterLast($messageKey, '.');
        }

        return 'NO MESSAGE';
    }

}
