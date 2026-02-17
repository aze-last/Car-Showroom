<?php

namespace App\Livewire;

use App\Models\Unit;
use App\Models\UnitStatusLog;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class AdminDashboard extends Component
{
    public function render(): View
    {
        Gate::authorize('viewAny', Unit::class);

        $totalUnits = Unit::query()->count();
        $availableUnits = Unit::query()
            ->where('status', Unit::STATUS_AVAILABLE)
            ->count();
        $soldUnits = Unit::query()
            ->where('status', Unit::STATUS_SOLD)
            ->count();
        $addedThisWeek = Unit::query()
            ->where('created_at', '>=', now()->startOfWeek())
            ->count();

        $availablePercentage = $totalUnits > 0
            ? (int) round(($availableUnits / $totalUnits) * 100)
            : 0;

        $recentLogs = UnitStatusLog::query()
            ->with(['unit', 'user'])
            ->latest()
            ->limit(10)
            ->get();

        return view('livewire.admin-dashboard', [
            'totalUnits' => $totalUnits,
            'availableUnits' => $availableUnits,
            'soldUnits' => $soldUnits,
            'addedThisWeek' => $addedThisWeek,
            'availablePercentage' => $availablePercentage,
            'recentLogs' => $recentLogs,
        ])->layout('layouts.admin-panel', [
            'title' => 'Dashboard',
        ]);
    }
}
