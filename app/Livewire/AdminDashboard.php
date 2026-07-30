<?php

namespace App\Livewire;

use App\Models\Auction;
use App\Models\BidDeposit;
use App\Models\Unit;
use App\Models\UnitStatusLog;
use App\Models\UnitView;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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

        // Distinct open conversations (user + unit pairs). Counted in PHP because
        // COUNT(DISTINCT col_a, col_b) is MySQL-only and tests run on sqlite.
        $activeInquiriesCount = \App\Models\ChatMessage::query()
            ->where('is_from_admin', false)
            ->whereNull('read_at')
            ->select('user_id', 'unit_id')
            ->groupBy('user_id', 'unit_id')
            ->get()
            ->count();

        $activeAuctionsCount = Auction::query()
            ->whereIn('status', ['live', 'active'])
            ->where('end_at', '>', now())
            ->count();

        $nextAuctionEndingAt = Auction::query()
            ->whereIn('status', ['live', 'active'])
            ->where('end_at', '>', now())
            ->orderBy('end_at')
            ->value('end_at');

        $pendingDepositsCount = BidDeposit::query()
            ->where('status', 'pending')
            ->count();

        $resolvedDepositsCount = BidDeposit::query()
            ->where('status', '!=', 'pending')
            ->count();

        $availablePercentage = $totalUnits > 0
            ? (int) round(($availableUnits / $totalUnits) * 100)
            : 0;

        // Portfolio Velocity (units sold per month, last 6 months) — rendered
        // client-side by Chart.js; this component only supplies the series.
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
            ];
        }

        $velocityChart = [
            'labels' => array_column($velocityData, 'label'),
            'data' => array_column($velocityData, 'count'),
            'accent' => '#000000',
            'fillFrom' => 'rgba(0, 0, 0, 0.06)',
            'yLabel' => 'Units Sold',
            'unitLabel' => 'sold',
        ];
        $velocityHasData = array_sum($velocityChart['data']) > 0;

        // Views Over Time (daily unit views, last 30 days)
        $viewsPerDay = UnitView::viewsPerDay(30);
        $viewsChart = [
            'labels' => $viewsPerDay->map(fn (array $day) => Carbon::parse($day['date'])->format('M j'))->all(),
            'data' => $viewsPerDay->pluck('count')->all(),
            'accent' => '#10b981',
            'fillFrom' => 'rgba(16, 185, 129, 0.10)',
            'yLabel' => 'Views',
            'unitLabel' => 'views',
        ];
        $viewsHasData = array_sum($viewsChart['data']) > 0;

        // Engagement leaderboards (top 5 each; only units with activity)
        $weekStart = now()->subDays(7);

        $mostViewedThisWeek = Unit::query()
            ->withCount(['views as views_last_week_count' => fn ($q) => $q->where('viewed_at', '>=', $weekStart)])
            ->whereHas('views', fn ($q) => $q->where('viewed_at', '>=', $weekStart))
            ->orderByDesc('views_last_week_count')
            ->limit(5)
            ->get();

        $mostFavoritedUnits = Unit::query()
            ->withCount('savedByUsers')
            ->has('savedByUsers')
            ->orderByDesc('saved_by_users_count')
            ->limit(5)
            ->get();

        // Weekly funnel readout: views → favorites → sold. "Sold this week"
        // uses updated_at like the monthly sales trend above; favorites come
        // from the saved_units pivot (no dedicated model exists for it).
        $viewsThisWeek = UnitView::query()->where('viewed_at', '>=', $weekStart)->count();
        $favoritesThisWeek = DB::table('saved_units')->where('created_at', '>=', $weekStart)->count();
        $soldThisWeek = Unit::query()
            ->where('status', Unit::STATUS_SOLD)
            ->where('updated_at', '>=', $weekStart)
            ->count();

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
            'nextAuctionEndingAt' => $nextAuctionEndingAt,
            'pendingDepositsCount' => $pendingDepositsCount,
            'resolvedDepositsCount' => $resolvedDepositsCount,
            'availablePercentage' => $availablePercentage,
            'velocityChart' => $velocityChart,
            'velocityHasData' => $velocityHasData,
            'viewsChart' => $viewsChart,
            'viewsHasData' => $viewsHasData,
            'mostViewedThisWeek' => $mostViewedThisWeek,
            'mostFavoritedUnits' => $mostFavoritedUnits,
            'viewsThisWeek' => $viewsThisWeek,
            'favoritesThisWeek' => $favoritesThisWeek,
            'soldThisWeek' => $soldThisWeek,
            'recentLogs' => $recentLogs,
            'recentInquiries' => $recentInquiries,
        ])->layout('layouts.admin-panel', [
            'title' => 'Admin Overview',
        ]);
    }
}
