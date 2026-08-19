<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;

class AuditService
{
    public static function log(
        string $action,
        string $table,
        ?int $recordId = null,
        ?array $details = null,
        ?User $user = null,
    ): AuditLog {
        return AuditLog::create([
            'user_id' => $user?->id ?? auth()->id(),
            'action' => $action,
            'affected_table' => $table,
            'record_id' => $recordId,
            'details' => $details,
            'source_ip' => request()->ip(),
            'created_at' => now(),
        ]);
    }
}
