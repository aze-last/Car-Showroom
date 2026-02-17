<?php

namespace App\Services;

use App\Models\Unit;
use App\Models\UnitStatusLog;

class UnitInventoryLogService
{
    public function record(
        Unit $unit,
        int $userId,
        string $action,
        array $changes = [],
        ?string $fromStatus = null,
        ?string $toStatus = null,
        ?string $requestId = null,
        ?string $reason = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): UnitStatusLog {
        return UnitStatusLog::query()->create([
            'unit_id' => $unit->id,
            'user_id' => $userId,
            'action' => $action,
            'from_status' => $fromStatus ?? $unit->status,
            'to_status' => $toStatus ?? $unit->status,
            'request_id' => $requestId,
            'reason' => $reason,
            'changes' => empty($changes) ? null : $changes,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }
}
