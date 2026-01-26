<?php

namespace App\Jobs\AgriPulse;

use App\Models\User;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\AgriPulsePostReport;
use App\Models\AgriPulseModerationLog;
use App\Services\AgriPulse\ModerationExportService;
use App\Jobs\AgriPulse\SendModerationNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class ExportModerationData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes
    public $tries = 3;

    protected $user;
    protected $type;
    protected $format;
    protected $ids;
    protected $filters;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $type, string $format, ?array $ids = null, array $filters = [])
    {
        $this->user = $user;
        $this->type = $type;
        $this->format = $format;
        $this->ids = $ids;
        $this->filters = $filters;
    }

    /**
     * Execute the job.
     */
    public function handle(ModerationExportService $exportService)
    {
        try {
            // Send start notification
            $this->sendStartNotification();

            // Query data based on type
            $data = $this->queryData();

            // Generate export file
            $filepath = $this->generateExport($exportService, $data);

            // Generate secure download URL (24-hour expiry)
            $downloadUrl = URL::temporarySignedRoute(
                'exports.download',
                now()->addHours(24),
                ['filename' => basename($filepath)]
            );

            // Send completion notification with download link
            $this->sendCompletionNotification($downloadUrl, $data->count());

        } catch (\Exception $e) {
            // Send failure notification
            $this->sendFailureNotification($e->getMessage());
            throw $e;
        }
    }

    /**
     * Query data based on type and filters.
     */
    private function queryData()
    {
        switch ($this->type) {
            case 'posts':
                return $this->queryPosts();
            case 'comments':
                return $this->queryComments();
            case 'reports':
                return $this->queryReports();
            case 'users':
                return $this->queryUsers();
            case 'logs':
                return $this->queryLogs();
            default:
                throw new \Exception("Unknown export type: {$this->type}");
        }
    }

    /**
     * Query posts with filters.
     */
    private function queryPosts()
    {
        $query = Post::with(['user', 'product'])
            ->withCount(['reactions', 'comments', 'reports'])
            ->withTrashed();

        // Apply IDs filter (bulk export)
        if ($this->ids && !empty($this->ids)) {
            $query->whereIn('id', $this->ids);
        }

        // Apply status filter
        if (isset($this->filters['status']) && $this->filters['status']) {
            $query->where('status', $this->filters['status']);
        }

        // Apply deleted filter
        if (isset($this->filters['deleted']) && $this->filters['deleted'] === true) {
            $query->onlyTrashed();
        }

        // Apply search filter
        if (isset($this->filters['search']) && $this->filters['search']) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        return $query->latest()->get();
    }

    /**
     * Query comments with filters.
     */
    private function queryComments()
    {
        $query = PostComment::with(['user', 'post.user']);

        // Apply IDs filter (bulk export)
        if ($this->ids && !empty($this->ids)) {
            $query->whereIn('id', $this->ids);
        }

        // Apply search filter
        if (isset($this->filters['search']) && $this->filters['search']) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        return $query->latest()->get();
    }

    /**
     * Query reports with filters.
     */
    private function queryReports()
    {
        $query = AgriPulsePostReport::with([
            'post' => function($q) {
                $q->withCount(['reactions', 'comments', 'reposts'])->with('user.roles');
            },
            'reporter.roles',
            'resolver'
        ]);

        // Apply IDs filter (bulk export)
        if ($this->ids && !empty($this->ids)) {
            $query->whereIn('id', $this->ids);
        }

        // Apply status filter
        if (isset($this->filters['status']) && $this->filters['status']) {
            $query->where('status', $this->filters['status']);
        }

        // Apply search filter
        if (isset($this->filters['search']) && $this->filters['search']) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                  ->orWhere('resolution_note', 'like', "%{$search}%")
                  ->orWhereHas('reporter', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        return $query->latest()->get();
    }

    /**
     * Query users with filters.
     */
    private function queryUsers()
    {
        $query = User::withCount(['posts', 'receivedReports', 'moderationActions']);

        // Apply IDs filter (bulk export)
        if ($this->ids && !empty($this->ids)) {
            $query->whereIn('id', $this->ids);
        }

        // Apply search filter
        if (isset($this->filters['search']) && $this->filters['search']) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
                
                if (is_numeric($search)) {
                    $q->orWhere('id', $search);
                }
            });
        }

        return $query->latest()->get();
    }

    /**
     * Query logs with filters.
     */
    private function queryLogs()
    {
        $query = AgriPulseModerationLog::with(['moderator', 'subject'])->latest();

        // Apply IDs filter (bulk export)
        if ($this->ids && !empty($this->ids)) {
            $query->whereIn('id', $this->ids);
        }

        // Apply action filter
        if (isset($this->filters['action']) && $this->filters['action']) {
            $query->where('action', $this->filters['action']);
        }

        // Apply moderator filter
        if (isset($this->filters['moderator_id']) && $this->filters['moderator_id']) {
            $query->where('moderator_id', $this->filters['moderator_id']);
        }

        // Apply search filter
        if (isset($this->filters['search']) && $this->filters['search']) {
            $search = $this->filters['search'];
            $query->whereHas('moderator', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    /**
     * Generate export file using dynamic service.
     */
    private function generateExport(ModerationExportService $exportService, $data): string
    {
        return $exportService->export($data, $this->type, $this->format, $this->filters);
    }

    /**
     * Send export start notification.
     */
    private function sendStartNotification()
    {
        $typeLabel = ucfirst($this->type);
        $formatLabel = strtoupper($this->format);
        $countLabel = $this->ids ? count($this->ids) . ' selected' : 'all';

        SendModerationNotification::dispatch($this->user, [
            'subject' => 'Export Started',
            'message' => "Your {$typeLabel} export ({$formatLabel} format, {$countLabel} records) has been started. We'll notify you when it's ready to download.",
            'priority' => 'normal',
            'action_url' => null,
            'image_url' => null,
            'content_details' => [
                'export_type' => $this->type,
                'export_format' => $this->format,
                'export_count' => $countLabel,
                'started_at' => now()->toIso8601String()
            ]
        ]);
    }

    /**
     * Send export completion notification with download link.
     */
    private function sendCompletionNotification(string $downloadUrl, int $recordCount)
    {
        $typeLabel = ucfirst($this->type);
        $formatLabel = strtoupper($this->format);

        SendModerationNotification::dispatch($this->user, [
            'subject' => 'Export Ready for Download',
            'message' => "Your {$typeLabel} export is ready! We've successfully exported {$recordCount} records in {$formatLabel} format. Click the link below to download your file. The download link will expire in 24 hours.",
            'priority' => 'success',
            'action_url' => null,
            'image_url' => null,
            'content_details' => [
                'export_type' => $this->type,
                'export_format' => $this->format,
                'record_count' => $recordCount,
                'download_url' => $downloadUrl,
                'expires_at' => now()->addHours(24)->toIso8601String(),
                'completed_at' => now()->toIso8601String()
            ]
        ]);
    }

    /**
     * Send export failure notification.
     */
    private function sendFailureNotification(string $error)
    {
        $typeLabel = ucfirst($this->type);

        SendModerationNotification::dispatch($this->user, [
            'subject' => 'Export Failed',
            'message' => "We encountered an issue while generating your {$typeLabel} export. Please try again or contact support if the problem persists.",
            'priority' => 'critical',
            'action_url' => null,
            'image_url' => null,
            'content_details' => [
                'export_type' => $this->type,
                'export_format' => $this->format,
                'error' => $error,
                'failed_at' => now()->toIso8601String()
            ]
        ]);
    }
}
