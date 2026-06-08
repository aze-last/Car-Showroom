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

        // Calculate Trends (current month vs last month)
        $thisMonthStart = now()->startOfMonth();
        $lastMonthStart = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();

        $unitsThisMonth = Unit::where('created_at', '>=', $thisMonthStart)->count();
        $unitsLastMonth = Unit::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $unitTrend = $unitsLastMonth > 0 ? (($unitsThisMonth - $unitsLastMonth) / $unitsLastMonth) * 100 : 0;

        $portfolioValue = Unit::query()
            ->where('status', Unit::STATUS_AVAILABLE)
            ->sum('price_php');

        $totalSales = Unit::query()
            ->where('status', Unit::STATUS_SOLD)
            ->sum('price_php');

        $salesThisMonth = Unit::where('status', Unit::STATUS_SOLD)
            ->where('updated_at', '>=', $thisMonthStart)
            ->sum('price_php');
        $salesLastMonth = Unit::where('status', Unit::STATUS_SOLD)
            ->whereBetween('updated_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('price_php');
        $salesTrend = $salesLastMonth > 0 ? (($salesThisMonth - $salesLastMonth) / $salesLastMonth) * 100 : 0;

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

        // Calculate Portfolio Velocity (Sales per month for the last 6 months)
        $velocityData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = Unit::query()
                ->where('status', Unit::STATUS_SOLD)
                ->whereYear('updated_at', $month->year)
                ->whereMonth('updated_at', $month->month)
                ->count();

            $velocityData[] = [
                'label' => $month->format('M'),
                'count' => $count,
                'x' => (5 - $i) * 200, // For SVG path generation
            ];
        }

        // Generate SVG Path for the velocity chart
        $maxCount = collect($velocityData)->max('count') ?: 1;
        $points = [];
        foreach ($velocityData as $index => $data) {
            $x = $index * (1000 / 5);
            $y = 250 - ($data['count'] / $maxCount * 200);
            $points[] = "$x,$y";
        }
        $chartPath = 'M '.implode(' L ', $points);
        // Smooth curve calculation (simple version)
        $curvePath = 'M '.$points[0];
        for ($i = 0; $i < count($points) - 1; $i++) {
            $curr = explode(',', $points[$i]);
            $next = explode(',', $points[$i + 1]);
            $cx = ($curr[0] + $next[0]) / 2;
            $curvePath .= " C $cx,{$curr[1]} $cx,{$next[1]} {$next[0]},{$next[1]}";
        }

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
            'unitTrend' => $unitTrend,
            'salesTrend' => $salesTrend,
            'activeInquiriesCount' => $activeInquiriesCount,
            'activeAuctionsCount' => $activeAuctionsCount,
            'availablePercentage' => $availablePercentage,
            'velocityData' => $velocityData,
            'chartPath' => $curvePath,
            'recentLogs' => $recentLogs,
            'recentInquiries' => $recentInquiries,
        ])->layout('layouts.admin-panel', [
            'title' => 'Admin Overview',
        ]);
    }
}
