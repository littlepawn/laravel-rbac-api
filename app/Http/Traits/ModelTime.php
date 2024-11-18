<?php

namespace App\Http\Traits;


trait ModelTime
{
    protected function serializeDate($date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getLoginAtAttribute($value)
    {
        return $value ?? '';
    }

    public function getCreatedAtAttribute($value)
    {
        return $value ?? '';
    }
    public function getUpdatedAtAttribute($value)
    {
        return $value ?? '';
    }

    public function getDeletedAtAttribute($value)
    {
        return $value ?? '';
    }
}
