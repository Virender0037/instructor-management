<?php

use App\Models\AuditLog;

if (!function_exists('audit')) {
    function audit(string $action, $subject = null, array $meta = []): void
    {
        $user = auth()->user();
        if (!$user) return;

        AuditLog::create([
            'actor_id' => $user->id,
            'action' => $action,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->id,
            'meta' => $meta ?: null,
        ]);
    }
}
