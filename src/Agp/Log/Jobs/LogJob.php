<?php

namespace Agp\Log\Jobs;

use Agp\Log\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Log
     */
    private $log;

    /**
     * Create a new job instance.
     *
     * @param Log $log
     */
    public function __construct(Log $log)
    {
        $this->log = $log;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->log->make();
    }
}
