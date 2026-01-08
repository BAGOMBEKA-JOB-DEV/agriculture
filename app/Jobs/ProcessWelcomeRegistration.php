<?php

namespace App\Jobs;

use App\Mail\WelcomeRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ProcessWelcomeRegistration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('ProcessWelcomeRegistration: Sending welcome email', [
                'user_id' => $this->user->id,
                'email' => $this->user->email,
            ]);

            Mail::to($this->user->email)->send(new WelcomeRegistration($this->user));

            Log::info('ProcessWelcomeRegistration: Welcome email sent successfully');
        } catch (\Exception $e) {
            Log::error('ProcessWelcomeRegistration: Failed to send welcome email', [
                'error' => $e->getMessage(),
                'user_id' => $this->user->id
            ]);
            throw $e;
        }
    }
}
