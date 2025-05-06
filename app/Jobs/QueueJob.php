<?php

namespace App\Jobs;

class QueueJob
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $jobs = \DB::table('jobs')->get();

        foreach ($jobs as $job) {
            \Artisan::call('queue:work --queue=' . $job->queue . ' --once');
        }
    }
}
