<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuditService
{
    /**
     * Log an auditable action to the database and the audit log channel.
     * CBUAE Requirement: All actions logged with IP and timestamp.
     */
    public function log(
        string  $action,
        Request $request,
        ?User   $user        = null,
        string  $status      = AuditLog::STATUS_SUCCESS,
        ?string $description = null,
        array   $metadata    = [],
        ?string $email       = null,
    ): AuditLog {
        $record = AuditLog::create([
            'user_id'    => $user?->id,
            'email'      => $email ?? $user?->email,
            'action'     => $action,
            'status'     => $status,
            'description'=> $description,
            'metadata'   => empty($metadata) ? null : $metadata,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'request_id' => $request->header('X-Request-ID') ?? uniqid('req_'),
            'session_id' => session()->getId(),
        ]);

        // Also write to the dedicated audit log file/channel
        Log::channel('audit')->info($action, [
            'audit_id'   => $record->id,
            'user_id'    => $user?->id,
            'email'      => $email ?? $user?->email,
            'status'     => $status,
            'ip'         => $request->ip(),
            'description'=> $description,
            'metadata'   => $metadata,
        ]);

        return $record;
    }
}
