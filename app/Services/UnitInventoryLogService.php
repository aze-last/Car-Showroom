<?php

namespace App\Services;

use App\Models\Unit;
use App\Models\UnitStatusLog;

class UnitInventoryLogService
{
    public function logCreation(Unit $unit, int $userId): UnitStatusLog
    {
        return $this->record(
            unit: $unit,
            userId: $userId,
            action: UnitStatusLog::ACTION_CREATE,
            toStatus: $unit->status,
        );
    }

    public function logUpdate(Unit $unit, int $userId, array $oldData, array $changes): UnitStatusLog
    {
        return $this->record(
            unit: $unit,
            userId: $userId,
            action: UnitStatusLog::ACTION_UPDATE,
            changes: [
                'before' => $oldData,
                'after' => $changes,
            ]
        );
    }

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
