<?php

namespace App\Jobs;

use App\Mail\AccountInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ProcessUserInvitation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $email;

    /**
     * Create a new job instance.
     */
    public function __construct(string $email)
    {
        $this->email = $email;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            \Log::info('ProcessUserInvitation: Starting email send', [
                'email' => $this->email
            ]);

            Mail::to($this->email)->send(new AccountInvitation($this->email));
            
            \Log::info('ProcessUserInvitation: Email sent successfully', [
                'email' => $this->email
            ]);
        } catch (\Exception $e) {
            \Log::error('ProcessUserInvitation: Failed to send email', [
                'email' => $this->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e; // Re-throw to mark job as failed
        }
    }
}
