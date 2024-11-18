<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserLoginEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var array
     */
    public array $loginInfo;

    /**
     * @param array $loginInfo
     */
    public function __construct(array $loginInfo)
    {
        $this->loginInfo = $loginInfo;
    }
}
