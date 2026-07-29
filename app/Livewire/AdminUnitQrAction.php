<?php

namespace App\Livewire;

use App\Concerns\HandlesLivewireErrors;
use App\Models\Unit;
use App\Models\UnitStatusLog;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

class AdminUnitQrAction extends Component
{
    use HandlesLivewireErrors;
    use WithFileUploads;

    public Unit $unit;

    public ?UnitStatusLog $lastLog = null;

    public string $qrSvg = '';

    public ?string $reason = null;

    public bool $is_guest = true;

    public ?int $buyer_id = null;

    public ?string $guest_name = null;

    public ?string $guest_contact = null;

    public $handover_image;

    public string $collector_search = '';

    #[Url]
    public ?string $action = null;

    public function mount(Unit $unit): void
    {
        Gate::authorize('changeStatus', $unit);

        $this->unit = $unit->load('category');
        $this->action = request()->query('action');
        $this->refreshUnitData();
        $this->generateQrSvg();
    }

    #[Computed]
    public function users()
    {
        if ($this->is_guest) {
            return collect();
        }

        $query = \App\Models\User::query()
            ->customers()
            ->orderBy('name');

        if (! empty(trim($this->collector_search))) {
            $search = '%'.trim($this->collector_search).'%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search);
            });
        }

        return $query->limit(50)->get();
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

        $result = $this->safely(fn () => $statusService->setSold(
            unit: $this->unit,
            userId: (int) auth()->id(),
            reason: $this->reason,
            ipAddress: request()->ip(),
            userAgent: request()->userAgent(),
        ), 'Could not mark the unit as sold. Please try again.', ['unit_id' => $this->unit->id]);

        if ($result === null) {
            return;
        }

        if ($result && $this->is_guest) {
            // The sale is already recorded — handover capture failures are
            // reported without undoing the status change.
            $this->safely(function () {
                $path = $this->handover_image->store('units/handovers', 'public');
                $this->unit->update([
                    'guest_name' => $this->guest_name,
                    'guest_contact' => $this->guest_contact,
                    'handover_image_path' => $path,
                ]);
            }, 'Unit marked as sold, but the handover details could not be saved. Please edit the unit to add them.', [
                'unit_id' => $this->unit->id,
            ]);
        } elseif ($result && $this->buyer_id) {
            $this->safely(function () {
                $this->unit->update(['buyer_id' => $this->buyer_id]);

                $buyer = \App\Models\User::find($this->buyer_id);
                if ($buyer) {
                    $buyer->notify(new \App\Notifications\UnitAcquiredNotification([
                        'message' => "Congratulations! You have successfully acquired the {$this->unit->name}.",
                        'unit_id' => $this->unit->id,
                        'unit_name' => $this->unit->name,
                    ]));
                }
            }, 'Unit marked as sold, but the buyer could not be assigned or notified.', [
                'unit_id' => $this->unit->id,
                'buyer_id' => $this->buyer_id,
            ]);
        }

        $this->reason = null;
        $this->guest_name = null;
        $this->guest_contact = null;
        $this->handover_image = null;
        $this->buyer_id = null;
        $this->collector_search = '';
        $this->is_guest = true;
        $this->refreshUnitData();

        session()->flash($result['changed'] ? 'status' : 'info', $result['message']);
    }

    public function markAsAvailable(\App\Services\UnitStatusService $statusService): void
    {
        Gate::authorize('changeStatus', $this->unit);

        $result = $this->safely(fn () => $statusService->setAvailable(
            unit: $this->unit,
            userId: (int) auth()->id(),
            reason: $this->reason,
            ipAddress: request()->ip(),
            userAgent: request()->userAgent(),
        ), 'Could not mark the unit as available. Please try again.', ['unit_id' => $this->unit->id]);

        if ($result === null) {
            return;
        }

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
            'users' => $this->users,
        ])->layout('layouts.admin-panel', [
            'title' => 'Unit Status Action',
        ]);
    }
}
