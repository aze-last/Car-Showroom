<?php

namespace App\Livewire;

use App\Models\Unit;
use App\Models\UnitStatusLog;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class AdminUnitQrAction extends Component
{
    public Unit $unit;

    public ?UnitStatusLog $lastLog = null;

    public string $qrSvg = '';

    public ?string $reason = null;

    public ?int $buyer_id = null;

    public function mount(Unit $unit): void
    {
        Gate::authorize('changeStatus', $unit);

        $this->unit = $unit->load('category');
        $this->refreshUnitData();
        $this->generateQrSvg();
    }

    public function markAsSold(\App\Services\UnitStatusService $statusService): void
    {
        Gate::authorize('changeStatus', $this->unit);

        $result = $statusService->setSold(
            unit: $this->unit,
            userId: (int) auth()->id(),
            reason: $this->reason,
            ipAddress: request()->ip(),
            userAgent: request()->userAgent(),
        );

        if ($result && $this->buyer_id) {
            $this->unit->update(['buyer_id' => $this->buyer_id]);

            $buyer = \App\Models\User::find($this->buyer_id);
            if ($buyer) {
                $buyer->notify(new \App\Notifications\UnitAcquiredNotification([
                    'message' => "Congratulations! You have successfully acquired the {$this->unit->name}.",
                    'unit_id' => $this->unit->id,
                    'unit_name' => $this->unit->name,
                ]));
            }
        }

        $this->reason = null;
        $this->refreshUnitData();

        session()->flash($result['changed'] ? 'status' : 'info', $result['message']);
    }

    public function markAsAvailable(\App\Services\UnitStatusService $statusService): void
    {
        Gate::authorize('changeStatus', $this->unit);

        $result = $statusService->setAvailable(
            unit: $this->unit,
            userId: (int) auth()->id(),
            reason: $this->reason,
            ipAddress: request()->ip(),
            userAgent: request()->userAgent(),
        );

        $this->reason = null;
        $this->refreshUnitData();

        session()->flash($result['changed'] ? 'status' : 'info', $result['message']);
    }

    private function refreshUnitData(): void
    {
        $this->unit->refresh();
        $this->lastLog = $this->unit->statusLogs()->with('user')->first();
    }

    private function generateQrSvg(): void
    {
        $renderer = new ImageRenderer(
            new RendererStyle(280),
            new SvgImageBackEnd,
        );

        $writer = new Writer($renderer);
        $this->qrSvg = $writer->writeString($this->unit->signedQrUrl());
    }

    public function render(): View
    {
        return view('livewire.admin-unit-qr-action', [
            'users' => \App\Models\User::query()->orderBy('name')->get(),
        ])->layout('layouts.admin-panel', [
            'title' => 'Unit Status Action',
        ]);
    }
}
