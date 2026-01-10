<?php

namespace App\Jobs;

use App\Mail\AccountRestored;
use App\Mail\AccountRevoked;
use App\Mail\AuthorityElevated;
use App\Mail\IdentityVerified;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyGovernanceAction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $action;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param string $action
     */
    public function __construct(User $user, string $action)
    {
        $this->user = $user;
        $this->action = $action;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $mailable = match($this->action) {
            'suspend' => new AccountRevoked($this->user),
            'activate' => new AccountRestored($this->user),
            'verify' => new IdentityVerified($this->user),
            'promote' => new AuthorityElevated($this->user),
            default => null
        };

        if ($mailable) {
            Mail::to($this->user->email)->send($mailable);
        }
    }
}
