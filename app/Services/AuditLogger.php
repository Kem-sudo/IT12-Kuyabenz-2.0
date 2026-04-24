<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogger
{
    public static function log(string $action, array $metadata = [], ?Request $request = null, ?string $subjectType = null, $subjectId = null): void
    {
        $request = $request ?: request();
        $user = auth()->user();

        AuditLog::create([
            'user_id' => $user?->id,
            'role' => $user?->role,
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId !== null ? (string) $subjectId : null,
            'metadata' => $metadata ?: null,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'created_at' => now(),
        ]);
    }
}

