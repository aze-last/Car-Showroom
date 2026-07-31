<?php

namespace App\Services;

use App\Models\Unit;
use App\Models\UnitStatusLog;

class AuctionReadinessService
{
    /**
     * Minimum number of completed sales required to compute a category benchmark.
     */
    public const MIN_COMPLETED_SALES_FOR_BENCHMARK = 3;

    /**
     * Evaluate a unit's auction readiness and suggested pricing.
     *
     * @return array{
     *     is_candidate: bool,
     *     days_listed: int,
     *     recommendation_status: string,
     *     recommendation_label: string,
     *     suggested_reserve_php: ?int,
     *     suggested_starting_bid_php: ?int,
     *     category_benchmark_days: ?float,
     *     benchmark_comparison: ?string,
     *     engagement_adjusted: bool,
     *     engagement_note: ?string,
     *     disclaimer: string
     * }
     */
    public function evaluate(Unit $unit): array
    {
        $daysListed = $unit->daysInCurrentListing();
        $categoryBenchmark = $unit->category_id ? $this->getCategoryBenchmark($unit->category_id) : null;

        $benchmarkComparison = null;
        if ($categoryBenchmark !== null && $categoryBenchmark > 0) {
            $ratio = round($daysListed / $categoryBenchmark, 1);
            $benchmarkComparison = "Listed {$ratio}x category average ({$categoryBenchmark} days)";
        } elseif ($unit->category_id) {
            $benchmarkComparison = 'Not enough sales history yet';
        }

        if ($daysListed <= 30 || $unit->price_php === null || $unit->price_php <= 0) {
            return [
                'is_candidate' => false,
                'days_listed' => $daysListed,
                'recommendation_status' => 'still_fresh',
                'recommendation_label' => 'Still fresh, auction not recommended',
                'suggested_reserve_php' => null,
                'suggested_starting_bid_php' => null,
                'category_benchmark_days' => $categoryBenchmark,
                'benchmark_comparison' => $benchmarkComparison,
                'engagement_adjusted' => false,
                'engagement_note' => null,
                'disclaimer' => 'Internal dealership inventory heuristic based on sitting days. Not an official market valuation.',
            ];
        }

        // Tiers:
        // 31-60 days: Reserve ~ 90% of price_php, starting bid ~ 65% of reserve
        // 61-90 days: Reserve ~ 80% of price_php, starting bid ~ 60% of reserve
        // 90+ days:   Reserve ~ 70% of price_php, starting bid ~ 55% of reserve
        if ($daysListed <= 60) {
            $baseReserveRatio = 0.90;
            $startingBidRatio = 0.65;
            $recommendationStatus = 'moderate_candidate';
            $recommendationLabel = 'Auction Candidate (31–60 Days Listed)';
        } elseif ($daysListed <= 90) {
            $baseReserveRatio = 0.80;
            $startingBidRatio = 0.60;
            $recommendationStatus = 'strong_candidate';
            $recommendationLabel = 'Strong Auction Candidate (61–90 Days Listed)';
        } else {
            $baseReserveRatio = 0.70;
            $startingBidRatio = 0.55;
            $recommendationStatus = 'prime_candidate';
            $recommendationLabel = 'Prime Auction Candidate (90+ Days Listed)';
        }

        // Soft adjustment for high engagement (views / favorites):
        // If views >= 20 or favorites >= 2, apply +5% reserve ratio boost (less aggressive discount)
        $viewsCount = (int) ($unit->views_count ?? $unit->views()->count());
        $savedCount = (int) ($unit->saved_by_users_count ?? $unit->savedByUsers()->count());

        $engagementAdjusted = false;
        $engagementNote = null;

        if ($viewsCount >= 20 || $savedCount >= 2) {
            $baseReserveRatio = min(0.95, $baseReserveRatio + 0.05);
            $engagementAdjusted = true;
            $engagementNote = 'High interest detected (+5% reserve boost)';
        }

        // Hard Floor: Reserve never below 50% of price_php
        $finalReserveRatio = max(0.50, $baseReserveRatio);

        $suggestedReserve = (int) round(($unit->price_php * $finalReserveRatio) / 1000) * 1000;
        $suggestedStartingBid = (int) round(($suggestedReserve * $startingBidRatio) / 1000) * 1000;

        return [
            'is_candidate' => true,
            'days_listed' => $daysListed,
            'recommendation_status' => $recommendationStatus,
            'recommendation_label' => $recommendationLabel,
            'suggested_reserve_php' => $suggestedReserve,
            'suggested_starting_bid_php' => $suggestedStartingBid,
            'category_benchmark_days' => $categoryBenchmark,
            'benchmark_comparison' => $benchmarkComparison,
            'engagement_adjusted' => $engagementAdjusted,
            'engagement_note' => $engagementNote,
            'disclaimer' => 'Internal dealership inventory heuristic based on sitting days. Not an official market valuation.',
        ];
    }

    /**
     * Compute average time-to-sell (in days) for a given category.
     * Requires at least MIN_COMPLETED_SALES_FOR_BENCHMARK completed sales.
     */
    public function getCategoryBenchmark(int $categoryId): ?float
    {
        $soldLogs = UnitStatusLog::query()
            ->where('to_status', Unit::STATUS_SOLD)
            ->whereHas('unit', fn ($q) => $q->where('category_id', $categoryId))
            ->get();

        if ($soldLogs->isEmpty()) {
            return null;
        }

        $durations = [];

        foreach ($soldLogs as $soldLog) {
            /** @var UnitStatusLog|null $prevAvailableLog */
            $prevAvailableLog = UnitStatusLog::query()
                ->where('unit_id', $soldLog->unit_id)
                ->where('to_status', Unit::STATUS_AVAILABLE)
                ->where('created_at', '<=', $soldLog->created_at)
                ->latest('created_at')
                ->first();

            $startTime = $prevAvailableLog ? $prevAvailableLog->created_at : $soldLog->unit?->created_at;

            if ($startTime && $soldLog->created_at >= $startTime) {
                $durations[] = $startTime->diffInDays($soldLog->created_at);
            }
        }

        if (count($durations) < self::MIN_COMPLETED_SALES_FOR_BENCHMARK) {
            return null;
        }

        return round(array_sum($durations) / count($durations), 1);
    }
}
