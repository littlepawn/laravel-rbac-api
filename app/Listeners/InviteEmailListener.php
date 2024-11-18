<?php

namespace App\Listeners;

use App\Events\InviteEmailEvent;
use App\Jobs\InviteEmailJob;
use Illuminate\Support\Facades\Log;

class InviteEmailListener
{
    /**
     * @param InviteEmailEvent $event
     * @return void
     */
    public function handle(InviteEmailEvent $event): void
    {

        try {
            dispatch(new InviteEmailJob($event->email))->onQueue('emails');
        } catch (\Throwable $e) {
            Log::error('Error dispatching job: ' . $e->getMessage());
        }
    }
}
