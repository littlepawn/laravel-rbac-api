<?php

namespace App\Models\Log;

use App\Http\Traits\ModelTime;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserLoginLog extends Model
{
    use HasFactory, ModelTime;

    protected $fillable = [
        'user_id',
        'role_name',
        'ip',
        'device',
    ];

    protected $hidden = [
        'id',
    ];

    public function user():hasOne
    {
        return $this->hasOne(User::class,'id','user_id');
    }

}
