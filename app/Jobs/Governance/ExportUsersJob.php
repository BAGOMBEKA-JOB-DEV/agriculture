<?php

namespace App\Jobs\Governance;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Exports\Governance\UserExport;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class ExportUsersJob implements ShouldQueue
{
    use Queueable;

    protected $format;
    protected $filters;
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct($format, $filters, $userId)
    {
        $this->format = $format;
        $this->filters = $filters;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $filename = 'governance_export_' . Str::random(10) . '.' . ($this->format === 'excel' ? 'xlsx' : $this->format);
        $path = 'exports/governance/' . $filename;

        if ($this->format === 'pdf') {
            $allowedRoles = ['super_admin', 'vendor', 'trader', 'buyer'];
            $query = User::with(['roles'])->whereHas('roles', fn($q) => $q->whereIn('slug', $allowedRoles));

            // Reuse filter logic from UserExport or create a common Trait
            if (!empty($this->filters['q'])) {
                $search = '%' . $this->filters['q'] . '%';
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'ILIKE', $search)
                      ->orWhere('email', 'ILIKE', $search);
                });
            }
            if (!empty($this->filters['role'])) {
                $query->whereHas('roles', fn($q) => $q->where('slug', $this->filters['role']));
            }
            if (!empty($this->filters['status'])) {
                $query->where('status', $this->filters['status']);
            }
            if (!empty($this->filters['location'])) {
                $query->where('location', 'ILIKE', '%' . $this->filters['location'] . '%');
            }

            $users = $query->get();
            $pdf = Pdf::loadView('exports.governance_users', compact('users'));
            Storage::put($path, $pdf->output());
        } else {
            // Excel or CSV
            Excel::store(new UserExport($this->filters), $path);
        }

        // Notify User
        // Notify User
        $user = User::find($this->userId);
        if ($user) {
            $url = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                'super-admin.download-export',
                now()->addHours(24),
                ['filename' => $filename]
            );
            
            // 1. Send Email (using new Mailable)
            \Illuminate\Support\Facades\Mail::to($user)->send(new \App\Mail\ExportReadyMail($user, $url, $this->format));

            // 2. Send In-App Notification (Database)
            $user->notify(new \App\Notifications\ExportReady(
                "Export Ready: Your governance data ({$this->format}) is prepared. [Click to download]({$url})",
                $url,
                'success'
            ));
        }
    }
}
