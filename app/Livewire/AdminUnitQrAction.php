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
use Livewire\WithFileUploads;

class AdminUnitQrAction extends Component
{
    use WithFileUploads;

    public Unit $unit;

    public ?UnitStatusLog $lastLog = null;

    public string $qrSvg = '';

    public ?string $reason = null;

    public bool $is_guest = false;

    public ?int $buyer_id = null;

    public ?string $guest_name = null;

    public ?string $guest_contact = null;

    public $handover_image;

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

        if ($this->is_guest) {
            $this->validate([
                'guest_name' => 'required|string|max:255',
                'guest_contact' => 'required|string|max:255',
                'handover_image' => 'required|image|max:10240', // 10MB max
            ]);
        } else {
            $this->validate([
                'buyer_id' => 'required|exists:users,id',
            ]);
        }

        $result = $statusService->setSold(
            unit: $this->unit,
            userId: (int) auth()->id(),
            reason: $this->reason,
            ipAddress: request()->ip(),
            userAgent: request()->userAgent(),
        );

        if ($result && $this->is_guest) {
            $path = $this->handover_image->store('units/handovers', 'public');
            $this->unit->update([
                'guest_name' => $this->guest_name,
                'guest_contact' => $this->guest_contact,
                'handover_image_path' => $path,
            ]);
        } elseif ($result && $this->buyer_id) {
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
