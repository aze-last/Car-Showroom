<?php

namespace App\Services;

use App\Models\Unit;
use App\Models\UnitStatusLog;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class UnitStatusService
{
    public function __construct(
        private readonly UnitInventoryLogService $inventoryLogService,
    ) {}

    /**
     * @return array{changed: bool, message: string, unit: Unit}
     */
    public function setSold(
        Unit $unit,
        int $userId,
        ?string $requestId = null,
        ?string $reason = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): array {
        return $this->setStatus(
            unit: $unit,
            targetStatus: Unit::STATUS_SOLD,
            userId: $userId,
            requestId: $requestId,
            reason: $reason,
            ipAddress: $ipAddress,
            userAgent: $userAgent,
        );
    }

    /**
     * @return array{changed: bool, message: string, unit: Unit}
     */
    public function setAvailable(
        Unit $unit,
        int $userId,
        ?string $requestId = null,
        ?string $reason = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): array {
        return $this->setStatus(
            unit: $unit,
            targetStatus: Unit::STATUS_AVAILABLE,
            userId: $userId,
            requestId: $requestId,
            reason: $reason,
            ipAddress: $ipAddress,
            userAgent: $userAgent,
        );
    }

    /**
     * @return array{changed: bool, message: string, unit: Unit}
     */
    public function setStatus(
        Unit $unit,
        string $targetStatus,
        int $userId,
        ?string $requestId = null,
        ?string $reason = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): array {
        if (! in_array($targetStatus, Unit::statuses(), true)) {
            throw new InvalidArgumentException('Invalid target status.');
        }

        return DB::transaction(function () use ($unit, $targetStatus, $userId, $requestId, $reason, $ipAddress, $userAgent): array {
            $lockedUnit = Unit::query()
                ->lockForUpdate()
                ->findOrFail($unit->id);

            if ($lockedUnit->status === $targetStatus) {
                return [
                    'changed' => false,
                    'message' => $targetStatus === Unit::STATUS_SOLD
                        ? 'Unit is already marked as sold.'
                        : 'Unit is already marked as available.',
                    'unit' => $lockedUnit,
                ];
            }

            $fromStatus = $lockedUnit->status;
            $lockedUnit->status = $targetStatus;
            $lockedUnit->save();

            $this->inventoryLogService->record(
                unit: $lockedUnit,
                userId: $userId,
                action: $this->actionFor($targetStatus),
                changes: [
                    'status' => [
                        'from' => $fromStatus,
                        'to' => $targetStatus,
                    ],
                ],
                fromStatus: $fromStatus,
                toStatus: $targetStatus,
                requestId: $requestId,
                reason: $reason,
                ipAddress: $ipAddress,
                userAgent: $userAgent,
            );

            return [
                'changed' => true,
                'message' => $targetStatus === Unit::STATUS_SOLD
                    ? 'Unit has been marked as sold.'
                    : 'Unit has been marked as available.',
                'unit' => $lockedUnit,
            ];
        }, 3);
    }

    private function actionFor(string $targetStatus): string
    {
        return $targetStatus === Unit::STATUS_SOLD
            ? UnitStatusLog::ACTION_SET_SOLD
            : UnitStatusLog::ACTION_SET_AVAILABLE;
    }
}
