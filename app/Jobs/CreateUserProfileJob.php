<?php

namespace App\Jobs;

use App\Models\Profil;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CreateUserProfileJob implements ShouldQueue
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
        Profil::create([
            'user_id' => $this->user->id,
            'bio' => 'create new profile to '. $this->user->name
        ]);
    }
}
