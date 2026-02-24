<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\WithoutRelations;
use Illuminate\Support\Facades\Mail;

#[WithoutRelations]

class SendEmailJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds before the job should be made available.
     *
     */
    // public $delay = 30;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    // public $uniqueFor = 20;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user)
    {
        // $this->queue = 'emailing';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::raw('welcome to queues site', function ($message) {
            $message->to($this->user->email)->subject('new register');
        });
    }


    /**
     * Get the unique ID for the job.
     */
    public function uniqueId()
    {
        return $this->user->id;
    }
}
