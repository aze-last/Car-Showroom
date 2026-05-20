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
        $this->unit = Unit::query()
            ->with('category')
            ->findOrFail($this->unit->id);

        $this->lastLog = $this->unit->statusLogs()
            ->with('user')
            ->first();
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
        return view('livewire.admin-unit-qr-action')
            ->layout('layouts.admin-panel', [
                'title' => 'QR Status Action',
            ]);
    }
}
