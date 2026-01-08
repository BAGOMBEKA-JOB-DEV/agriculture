<?php

namespace App\Jobs;

use App\Mail\UserCredentials;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ProcessUserCreation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $password;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            \Log::info('ProcessUserCreation: Starting email send', [
                'user_id' => $this->user->id,
                'email' => $this->user->email
            ]);

            Mail::to($this->user->email)->send(new UserCredentials($this->user, $this->password));
            
            \Log::info('ProcessUserCreation: Email sent successfully', [
                'user_id' => $this->user->id,
                'email' => $this->user->email
            ]);
        } catch (\Exception $e) {
            \Log::error('ProcessUserCreation: Failed to send email', [
                'user_id' => $this->user->id,
                'email' => $this->user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e; // Re-throw to mark job as failed
        }
    }
}
