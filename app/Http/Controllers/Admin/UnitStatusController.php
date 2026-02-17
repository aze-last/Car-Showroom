<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Services\UnitStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class UnitStatusController extends Controller
{
    public function setSold(Request $request, Unit $unit, UnitStatusService $statusService): RedirectResponse
    {
        $this->authorize('changeStatus', $unit);

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
            'request_id' => ['nullable', 'uuid'],
        ]);
        $requestId = $validated['request_id'] ?? (string) Str::uuid();

        try {
            $result = $statusService->setSold(
                unit: $unit,
                userId: (int) $request->user()->id,
                requestId: $requestId,
                reason: $validated['reason'] ?? null,
                ipAddress: $request->ip(),
                userAgent: $request->userAgent(),
            );

            return redirect()
                ->to($unit->signedQrUrl())
                ->with($result['changed'] ? 'status' : 'info', $result['message']);
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->to($unit->signedQrUrl())
                ->with('error', 'Status update could not be completed. Please retry.');
        }
    }

    public function setAvailable(Request $request, Unit $unit, UnitStatusService $statusService): RedirectResponse
    {
        $this->authorize('changeStatus', $unit);

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
            'request_id' => ['nullable', 'uuid'],
        ]);
        $requestId = $validated['request_id'] ?? (string) Str::uuid();

        try {
            $result = $statusService->setAvailable(
                unit: $unit,
                userId: (int) $request->user()->id,
                requestId: $requestId,
                reason: $validated['reason'] ?? null,
                ipAddress: $request->ip(),
                userAgent: $request->userAgent(),
            );

            return redirect()
                ->to($unit->signedQrUrl())
                ->with($result['changed'] ? 'status' : 'info', $result['message']);
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->to($unit->signedQrUrl())
                ->with('error', 'Status update could not be completed. Please retry.');
        }
    }
}
