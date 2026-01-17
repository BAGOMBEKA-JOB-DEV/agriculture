<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessPasswordResetMail implements ShouldQueue
{
    use Queueable;

    public $user;
    public $token;

    /**
     * Create a new job instance.
     */
    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \Illuminate\Support\Facades\Mail::to($this->user->email)
            ->send(new \App\Mail\PasswordResetMail($this->user, $this->token));
    }
}
