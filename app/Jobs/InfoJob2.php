<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class InfoJob2 implements ShouldQueue
{
    use Queueable;
    use Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // throw new Exception("Error Processing job 2", 1);

        Log::info("log info 2");
    }
}
