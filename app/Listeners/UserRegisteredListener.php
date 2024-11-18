<?php

namespace App\Listeners;

use App\Events\UserRegisteredEvent;
use App\Jobs\UserRegisteredEmailJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class UserRegisteredListener implements ShouldQueue
{
    /**
     * @param UserRegisteredEvent $event
     * @return void
     */
    public function handle(UserRegisteredEvent $event): void
    {
        try {
            //启动队列 php artisan queue:work
            dispatch(new UserRegisteredEmailJob($event->user))->onQueue('emails');
        } catch (\Throwable $e) {
            Log::error('Error dispatching job: ' . $e->getMessage());
        }

    }
}
