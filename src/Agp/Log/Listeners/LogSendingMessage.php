<?php

namespace Agp\Log\Listeners;

use Agp\Log\Jobs\LogJob;
use Agp\Log\Log;
use Illuminate\Mail\Events\MessageSending;

class LogSendingMessage
{
    public function handle(MessageSending $event)
    {
        $message = $event->message;
        LogJob::dispatch(new Log(7, $message));
    }
}
