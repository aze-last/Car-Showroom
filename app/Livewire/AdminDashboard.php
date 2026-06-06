<?php

namespace App\Livewire;

use App\Models\Inquiry;
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

        $portfolioValue = Unit::query()
            ->where('status', Unit::STATUS_AVAILABLE)
            ->sum('price_php');

        $totalSales = Unit::query()
            ->where('status', Unit::STATUS_SOLD)
            ->sum('price_php');

        $activeInquiriesCount = \App\Models\ChatMessage::query()
            ->where('is_from_admin', false)
            ->whereNull('read_at')
            ->distinct(['user_id', 'unit_id'])
            ->count();

        $activeAuctionsCount = \App\Models\Auction::query()
            ->where('status', 'live')
            ->count();

        $availablePercentage = $totalUnits > 0
            ? (int) round(($availableUnits / $totalUnits) * 100)
            : 0;

        $recentLogs = UnitStatusLog::query()
            ->with(['unit', 'user'])
            ->latest()
            ->limit(5)
            ->get();

        $recentInquiries = \App\Models\ChatMessage::query()
            ->where('is_from_admin', false)
            ->with(['user', 'unit'])
            ->latest()
            ->limit(5)
            ->get();

        return view('livewire.admin-dashboard', [
            'totalUnits' => $totalUnits,
            'availableUnits' => $availableUnits,
            'soldUnits' => $soldUnits,
            'portfolioValue' => $portfolioValue,
            'totalSales' => $totalSales,
            'activeInquiriesCount' => $activeInquiriesCount,
            'activeAuctionsCount' => $activeAuctionsCount,
            'availablePercentage' => $availablePercentage,
            'recentLogs' => $recentLogs,
            'recentInquiries' => $recentInquiries,
        ])->layout('layouts.admin-panel', [
            'title' => 'Admin Overview',
        ]);
    }
}
