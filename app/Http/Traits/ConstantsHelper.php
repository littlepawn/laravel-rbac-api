<?php

namespace App\Http\Traits;

use App\Constants\ResponseConstants;

trait ConstantsHelper
{
    /**
     * 获取所有状态码和消息的映射数组
     *
     * @return array 状态码和消息的映射数组
     */
    public static function getKeyValuePairs(): array
    {
        $reflection = new \ReflectionClass(self::class);
        $constants = $reflection->getConstants();
        $keyValuePairs = [];

        if (self::class === ResponseConstants::class) {
            $filename = 'common';
        } else {
            $filename = strtolower(str_replace('Constants', '', str_replace('App\Constants\response\\', '', self::class)));
        }
        foreach ($constants as $name => $value) {
            $keyValuePairs[$value] = 'response.' . $filename . '.' . strtoupper($name);
        }

        return $keyValuePairs;
    }
}
