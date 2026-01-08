<?php

namespace App\Jobs;

use App\Mail\LoginSecurityAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ProcessLoginSecurityAlert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $ipAddress;
    public $userAgent;
    public $time;

    /**
     * Create a new job instance.
     */
    public function __construct($user, $ipAddress, $userAgent, $time)
    {
        $this->user = $user;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->time = $time;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('ProcessLoginSecurityAlert: Sending security alert email', [
                'user_id' => $this->user->id,
                'email' => $this->user->email,
                'ip' => $this->ipAddress
            ]);

            Mail::to($this->user->email)->send(new LoginSecurityAlert(
                $this->user,
                $this->ipAddress,
                $this->userAgent,
                $this->time
            ));

            Log::info('ProcessLoginSecurityAlert: Security alert sent successfully');
        } catch (\Exception $e) {
            Log::error('ProcessLoginSecurityAlert: Failed to send security alert', [
                'error' => $e->getMessage(),
                'user_id' => $this->user->id
            ]);
            throw $e;
        }
    }
}
