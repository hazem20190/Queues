<?php

namespace App\Jobs;

use App\Models\Setting;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CreateUserSettingJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Setting::create([
            'user_id' => $this->user->id,
            'language' => 'ar',
            'timezone' => 'utc'
        ]);
        throw new Exception("error in register");

    }
}
