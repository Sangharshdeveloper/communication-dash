<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ArchiveAuditLogs extends Command
{
    protected $signature   = 'audit:archive {--days=90 : Archive logs older than this many days}';
    protected $description = 'Archive old audit logs to cold storage (CBUAE 7-year retention)';

    public function handle(): int
    {
        $days  = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $logs = AuditLog::where('created_at', '<', $cutoff)->get();

        if ($logs->isEmpty()) {
            $this->info('No audit logs to archive.');
            return self::SUCCESS;
        }

        $filename  = 'audit-archive-' . $cutoff->format('Y-m-d') . '.json';
        $content   = $logs->toJson(JSON_PRETTY_PRINT);

        // Store in the 'audit-archive' disk (configure in filesystems.php to point to secure storage)
        Storage::disk('local')->put("audit-archives/{$filename}", $content);

        $this->info("Archived {$logs->count()} audit log entries to audit-archives/{$filename}");

        // Optionally delete from DB after archiving (keep last 90 days in DB for fast queries)
        // AuditLog::where('created_at', '<', $cutoff)->delete();

        return self::SUCCESS;
    }
}
