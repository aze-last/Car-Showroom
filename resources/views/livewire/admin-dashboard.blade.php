@php
    use App\Models\UnitStatusLog;
@endphp

<div class="space-y-12 animate-showroom-fade-up" wire:poll.30s>
    <!-- Top Header Area -->
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-stack-lg">
        <div>
            <h2 class="text-4xl font-bold tracking-tight text-zinc-900 mb-1">Showroom Operations Command</h2>
            <p class="text-xs font-medium text-zinc-400">Actionable intelligence & daily task queue for {{ now()->format('M d, Y') }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.units.create') }}" class="inline-flex items-center gap-2 bg-zinc-900 text-white font-bold text-[11px] uppercase tracking-widest px-4 py-2.5 rounded-xl hover:bg-zinc-800 transition-all shadow-sm">
                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Add Vehicle
            </a>
            <a href="{{ route('admin.auctions.create') }}" class="inline-flex items-center gap-2 bg-zinc-100 text-zinc-900 font-bold text-[11px] uppercase tracking-widest px-4 py-2.5 rounded-xl hover:bg-zinc-200 transition-all">
                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2"><path d="M12 8V12L15 15M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                New Auction
            </a>
            <a href="{{ route('admin.deposits.index') }}" class="inline-flex items-center gap-2 bg-zinc-100 text-zinc-900 font-bold text-[11px] uppercase tracking-widest px-4 py-2.5 rounded-xl hover:bg-zinc-200 transition-all relative">
                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Verify Deposits
                @if($pendingDepositsCount > 0)
                    <span class="h-2 w-2 rounded-full bg-red-500"></span>
                @endif
            </a>
        </div>
    </header>

    <!-- Critical Alert Banner -->
    @if($pendingDepositsCount > 0)
        <a href="{{ route('admin.deposits.index') }}" class="flex items-center justify-between bg-white rounded-[32px] p-6 border-2 border-red-100 ambient-shadow hover-lift transition-all group">
            <div class="flex items-center gap-4">
                <div class="h-10 w-10 rounded-full bg-red-50 flex items-center justify-center shrink-0">
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-red-600" stroke="currentColor" stroke-width="2.5"><path d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <div>
                    <p class="text-[13px] font-bold text-black tracking-tight">{{ $pendingDepositsCount }} bid deposit{{ $pendingDepositsCount > 1 ? 's' : '' }} awaiting verification</p>
                    <p class="text-[11px] font-medium text-zinc-400">Collectors cannot join auctions until proof of payment is reviewed</p>
                </div>
            </div>
            <span class="text-[10px] font-bold text-red-600 uppercase tracking-widest group-hover:translate-x-1 transition-transform shrink-0">Review Now →</span>
        </a>
    @endif

    <!-- Priority Actions Grid -->
    <section>
        <h3 class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.4em] mb-6 pl-1">Priority Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Deposit Queue -->
            <a href="{{ route('admin.deposits.index') }}" class="bg-white rounded-[32px] p-8 border border-gallery-outline/20 ambient-shadow hover-lift relative overflow-hidden group block">
                <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity duration-300">
                    <svg viewBox="0 0 24 24" fill="none" class="h-20 w-20 text-black" stroke="currentColor" stroke-width="2.5"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0 1 12 2.944a11.955 11.955 0 0 1-8.618 3.04A12.02 12.02 0 0 0 3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.4em] mb-4">Deposit Queue</h3>
                <div class="text-5xl font-bold {{ $pendingDepositsCount > 0 ? 'text-red-600' : 'text-black' }} tracking-tighter mb-4">{{ $pendingDepositsCount }}</div>
                @php
                    $totalDeposits = $pendingDepositsCount + $resolvedDepositsCount;
                    $resolvedPercentage = $totalDeposits > 0 ? (int) round(($resolvedDepositsCount / $totalDeposits) * 100) : 100;
                @endphp
                <div class="h-1.5 w-full bg-zinc-100 rounded-full overflow-hidden mb-3">
                    <div class="h-full bg-emerald-500 rounded-full transition-all duration-500" style="width: {{ $resolvedPercentage }}%"></div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">{{ $resolvedPercentage }}% Processed</span>
                    <span class="text-[9px] font-bold text-black uppercase tracking-widest group-hover:translate-x-1 transition-transform">Verify →</span>
                </div>
            </a>

            <!-- Auction Room -->
            <a href="{{ route('admin.auctions.index') }}" class="bg-white rounded-[32px] p-8 border border-gallery-outline/20 ambient-shadow hover-lift relative overflow-hidden group block">
                <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity duration-300">
                    <svg viewBox="0 0 24 24" fill="none" class="h-20 w-20 text-black" stroke="currentColor" stroke-width="2.5"><path d="M12 8V12L15 15M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.4em] mb-4">Auction Room</h3>
                <div class="text-5xl font-bold text-black tracking-tighter mb-4">{{ $activeAuctionsCount }}</div>
                <div class="flex items-center gap-2 mb-3">
                    @if($activeAuctionsCount > 0)
                        <span class="bg-red-50 text-red-600 font-bold text-[9px] px-2 py-1 rounded-full flex items-center gap-1.5 uppercase tracking-widest">
                            <span class="relative flex h-1.5 w-1.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-red-500"></span>
                            </span>
                            Live Now
                        </span>
                    @else
                        <span class="bg-zinc-100 text-zinc-500 font-bold text-[9px] px-2 py-1 rounded-full uppercase tracking-widest">Idle</span>
                    @endif
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">
                        @if($nextAuctionEndingAt)
                            Ends {{ $nextAuctionEndingAt->diffForHumans() }}
                        @else
                            No lots closing
                        @endif
                    </span>
                    <span class="text-[9px] font-bold text-black uppercase tracking-widest group-hover:translate-x-1 transition-transform">Monitor →</span>
                </div>
            </a>

            <!-- Fleet Health -->
            <a href="{{ route('admin.units.index') }}" class="bg-white rounded-[32px] p-8 border border-gallery-outline/20 ambient-shadow hover-lift relative overflow-hidden group block">
                <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity duration-300">
                    <svg viewBox="0 0 24 24" fill="none" class="h-20 w-20 text-black" stroke="currentColor" stroke-width="2.5"><path d="M4 8.5L12 4L20 8.5V15.5L12 20L4 15.5V8.5Z" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.4em] mb-4">Fleet Health</h3>
                <div class="text-5xl font-bold text-black tracking-tighter mb-4">{{ $availablePercentage }}<span class="text-2xl text-zinc-300">%</span></div>
                <div class="h-1.5 w-full bg-zinc-100 rounded-full overflow-hidden mb-3">
                    <div class="h-full bg-emerald-500 rounded-full transition-all duration-500" style="width: {{ $availablePercentage }}%"></div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">{{ $availableUnits }} of {{ $totalUnits }} Available</span>
                    <span class="text-[9px] font-bold text-black uppercase tracking-widest group-hover:translate-x-1 transition-transform">Fleet →</span>
                </div>
            </a>

            <!-- Message Queue -->
            <a href="{{ route('admin.messages') }}" class="bg-white rounded-[32px] p-8 border border-gallery-outline/20 ambient-shadow hover-lift relative overflow-hidden group block">
                <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity duration-300">
                    <svg viewBox="0 0 24 24" fill="none" class="h-20 w-20 text-black" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.4em] mb-4">Unread Chats</h3>
                <div class="text-5xl font-bold text-black tracking-tighter mb-4">{{ $activeInquiriesCount }}</div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="bg-black text-white font-bold text-[9px] px-2 py-1 rounded-full uppercase tracking-widest">Messaging</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">{{ $activeInquiriesCount > 0 ? 'Collectors waiting' : 'Inbox clear' }}</span>
                    <span class="text-[9px] font-bold text-black uppercase tracking-widest group-hover:translate-x-1 transition-transform">Open →</span>
                </div>
            </a>
        </div>
    </section>

    <!-- Financial KPI Strip -->
    <section>
        <h3 class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.4em] mb-6 pl-1">Financial</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Portfolio Value -->
            <div class="bg-white rounded-[32px] p-8 border border-gallery-outline/20 ambient-shadow hover-lift relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity duration-300">
                    <svg viewBox="0 0 24 24" fill="none" class="h-20 w-20 text-black" stroke="currentColor" stroke-width="2.5"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M12 8v8M8 12h8"/></svg>
                </div>
                <h3 class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.4em] mb-4">Portfolio Value</h3>
                <div class="text-5xl font-bold text-black tracking-tighter mb-2">₱{{ number_format($portfolioValue / 1000000, 1) }}M</div>
                <div class="flex items-center gap-2">
                    <span class="bg-emerald-50 text-emerald-600 font-bold text-[9px] px-2 py-1 rounded-full uppercase tracking-widest">Available</span>
                </div>
            </div>

            <!-- Lifetime Sales -->
            <div class="bg-white rounded-[32px] p-8 border border-gallery-outline/20 ambient-shadow hover-lift relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity duration-300">
                    <svg viewBox="0 0 24 24" fill="none" class="h-20 w-20 text-black" stroke="currentColor" stroke-width="2.5"><path d="M21 15V19A2 2 0 0 1 19 21H5A2 2 0 0 1 3 19V15M7 10L12 15L17 10M12 15V3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.4em] mb-4">Lifetime Sales</h3>
                <div class="text-5xl font-bold text-black tracking-tighter mb-2">₱{{ number_format($totalSales / 1000000, 1) }}M</div>
                <div class="flex items-center gap-2">
                    <span class="bg-zinc-100 text-zinc-900 font-bold text-[9px] px-2 py-1 rounded-full uppercase tracking-widest">{{ $soldUnits }} Sold</span>
                    @if($salesTrend != 0)
                        <span class="text-[9px] font-bold {{ $salesTrend > 0 ? 'text-emerald-500' : 'text-red-500' }} uppercase tracking-widest">
                            {{ $salesTrend > 0 ? '↑' : '↓' }} {{ abs(round($salesTrend)) }}% vs last month
                        </span>
                    @endif
                </div>
            </div>

            <!-- Fleet Size -->
            <div class="bg-white rounded-[32px] p-8 border border-gallery-outline/20 ambient-shadow hover-lift relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity duration-300">
                    <svg viewBox="0 0 24 24" fill="none" class="h-20 w-20 text-black" stroke="currentColor" stroke-width="2.5"><path d="M4 8.5L12 4L20 8.5V15.5L12 20L4 15.5V8.5Z" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.4em] mb-4">Fleet Size</h3>
                <div class="text-5xl font-bold text-black tracking-tighter mb-2">{{ $totalUnits }}</div>
                <div class="flex items-center gap-2">
                    <span class="bg-zinc-100 text-zinc-900 font-bold text-[9px] px-2 py-1 rounded-full uppercase tracking-widest">Total Units</span>
                    @if($unitTrend != 0)
                        <span class="text-[9px] font-bold {{ $unitTrend > 0 ? 'text-emerald-500' : 'text-red-500' }} uppercase tracking-widest">
                            {{ $unitTrend > 0 ? '↑' : '↓' }} {{ abs(round($unitTrend)) }}% acquisition
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Main Data Grid (Chart & Timeline) -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Chart Area: Portfolio Velocity -->
        <div class="lg:col-span-8 bg-white rounded-[40px] p-10 border border-gallery-outline/20 ambient-shadow flex flex-col min-h-[500px]">
            <div class="flex justify-between items-center mb-12">
                <h3 class="text-[12px] font-bold text-black uppercase tracking-[0.4em]">Portfolio Velocity</h3>
                <span class="text-[10px] font-bold text-black px-4 py-1.5 bg-gallery-surface-high rounded-full uppercase tracking-widest">Last 6 Months</span>
            </div>

            <div class="flex-grow relative w-full">
                @if($velocityHasData)
                    <div wire:ignore x-data="adminLineChart(@js($velocityChart))" x-on:livewire:navigating.window="destroy()" class="absolute inset-0">
                        <canvas x-ref="canvas" aria-label="Units sold per month for the last 6 months"></canvas>
                    </div>
                @else
                    <div class="absolute inset-0 flex flex-col items-center justify-center gap-3 text-center">
                        <div class="h-12 w-12 rounded-full bg-gallery-surface-low flex items-center justify-center">
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-zinc-300" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18M7 16l4-6 4 3 5-8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <p class="text-[13px] font-bold text-black tracking-tight">Not enough sales data yet</p>
                        <p class="text-[11px] font-medium text-zinc-400 max-w-xs">The velocity curve will appear once units are marked as sold within the six-month window.</p>
                    </div>
                @endif
            </div>
            <div class="mt-10 pt-8 border-t border-gallery-outline/5 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="h-2 w-2 rounded-full bg-black"></div>
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Units Sold Per Month</span>
                </div>
                <div class="text-[10px] font-bold text-black uppercase tracking-widest italic opacity-60">Verified Analytics Hub</div>
            </div>
        </div>

        <!-- Audit Trail (Spans 4 cols) -->
        <div class="lg:col-span-4 bg-white rounded-[40px] p-10 border border-gallery-outline/20 ambient-shadow flex flex-col h-[500px] lg:h-auto">
            <div class="flex justify-between items-center mb-10">
                <h3 class="text-[12px] font-bold text-black uppercase tracking-[0.4em]">Curator Audit</h3>
                <a href="{{ route('admin.logs.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-zinc-300 hover:text-black transition-colors" stroke="currentColor" stroke-width="2.5"><path d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 1 1 0-2 1 1 0 0 1 0 2zm0 7a1 1 0 1 1 0-2 1 1 0 0 1 0 2zm0 7a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" stroke-linecap="round"/></svg>
                </a>
            </div>
            <div class="flex-grow overflow-y-auto pr-4 relative">
                <!-- Vertical Line -->
                <div class="absolute left-[11px] top-2 bottom-2 w-[2px] bg-gallery-surface-low"></div>
                <ul class="flex flex-col gap-10 relative z-10">
                    @forelse($recentLogs as $log)
                        <li class="flex gap-6 group">
                            <div class="w-6 h-6 rounded-full {{ $log->action === UnitStatusLog::ACTION_SET_AVAILABLE ? 'bg-emerald-500' : 'bg-black' }} flex items-center justify-center shrink-0 border-4 border-white mt-1 shadow-sm">
                                <svg viewBox="0 0 24 24" fill="none" class="h-2.5 w-2.5 text-white" stroke="currentColor" stroke-width="4">
                                    <path d="{{ $log->action === UnitStatusLog::ACTION_SET_AVAILABLE ? 'M20 6L9 17L4 12' : 'M6 18L18 6M6 6l12 12' }}" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[13px] font-bold text-black mb-1 tracking-tight">{{ $log->unit?->name ?? 'System Event' }}</p>
                                <p class="text-[11px] font-medium text-zinc-500 leading-snug">Status transitioned to <strong class="text-black">{{ $log->action }}</strong></p>
                                <p class="text-[9px] font-bold text-zinc-300 uppercase tracking-widest mt-2">{{ $log->created_at?->diffForHumans() }} • {{ $log->user?->name ?? 'Curator' }}</p>
                            </div>
                        </li>
                    @empty
                        <li class="text-[11px] font-medium text-zinc-400 pl-2">No activity recorded yet.</li>
                    @endforelse
                </ul>
            </div>
            <a href="{{ route('admin.logs.index') }}" class="mt-12 w-full h-12 rounded-full border border-gallery-outline/30 flex items-center justify-center text-[10px] font-bold text-black uppercase tracking-widest hover:bg-gallery-surface-low transition-all">
                Full Integrity Log
            </a>
        </div>
    </section>

    <!-- Engagement Grid (Views Chart & Leaderboards) -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Views Over Time -->
        <div class="lg:col-span-8 bg-white rounded-[40px] p-10 border border-gallery-outline/20 ambient-shadow flex flex-col min-h-[420px]">
            <div class="flex justify-between items-center mb-10">
                <h3 class="text-[12px] font-bold text-black uppercase tracking-[0.4em]">Views Over Time</h3>
                <span class="text-[10px] font-bold text-black px-4 py-1.5 bg-gallery-surface-high rounded-full uppercase tracking-widest">Last 30 Days</span>
            </div>

            <div class="flex-grow relative w-full">
                @if($viewsHasData)
                    <div wire:ignore x-data="adminLineChart(@js($viewsChart))" x-on:livewire:navigating.window="destroy()" class="absolute inset-0">
                        <canvas x-ref="canvas" aria-label="Unit page views per day for the last 30 days"></canvas>
                    </div>
                @else
                    <div class="absolute inset-0 flex flex-col items-center justify-center gap-3 text-center">
                        <div class="h-12 w-12 rounded-full bg-gallery-surface-low flex items-center justify-center">
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-zinc-300" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </div>
                        <p class="text-[13px] font-bold text-black tracking-tight">No views recorded yet</p>
                        <p class="text-[11px] font-medium text-zinc-400 max-w-xs">Daily traffic will chart here as collectors browse the public showroom.</p>
                    </div>
                @endif
            </div>

            <!-- Weekly Funnel Readout -->
            <div class="mt-10 pt-8 border-t border-gallery-outline/5 flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">This Week</span>
                    <span class="text-[12px] font-bold text-black tracking-tight">
                        {{ number_format($viewsThisWeek) }} {{ Str::plural('view', $viewsThisWeek) }}
                        <span class="text-zinc-300 mx-1">→</span>
                        {{ number_format($favoritesThisWeek) }} {{ Str::plural('favorite', $favoritesThisWeek) }}
                        <span class="text-zinc-300 mx-1">→</span>
                        {{ number_format($soldThisWeek) }} sold
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-2 w-2 rounded-full bg-emerald-500"></div>
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Unit Page Views Per Day</span>
                </div>
            </div>
        </div>

        <!-- Leaderboards -->
        <div class="lg:col-span-4 flex flex-col gap-8">
            <!-- Most Viewed This Week -->
            <div class="bg-white rounded-[40px] p-10 border border-gallery-outline/20 ambient-shadow flex-1">
                <h3 class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.4em] mb-8">Most Viewed This Week</h3>
                <ol class="flex flex-col gap-5">
                    @forelse($mostViewedThisWeek as $index => $unit)
                        <li>
                            <a href="{{ route('admin.units.edit', $unit) }}" class="flex items-center gap-4 group">
                                <span class="w-7 h-7 rounded-full {{ $index === 0 ? 'bg-black text-white' : 'bg-gallery-surface-low text-zinc-400' }} flex items-center justify-center text-[10px] font-bold shrink-0">{{ $index + 1 }}</span>
                                <span class="text-[13px] font-bold text-black tracking-tight truncate flex-1 group-hover:opacity-60 transition-opacity">{{ $unit->name }}</span>
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest shrink-0">{{ number_format($unit->views_last_week_count) }} {{ Str::plural('view', $unit->views_last_week_count) }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="text-[11px] font-medium text-zinc-400">No unit views in the last 7 days.</li>
                    @endforelse
                </ol>
            </div>

            <!-- Most Favorited -->
            <div class="bg-white rounded-[40px] p-10 border border-gallery-outline/20 ambient-shadow flex-1">
                <h3 class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.4em] mb-8">Most Favorited</h3>
                <ol class="flex flex-col gap-5">
                    @forelse($mostFavoritedUnits as $index => $unit)
                        <li>
                            <a href="{{ route('admin.units.edit', $unit) }}" class="flex items-center gap-4 group">
                                <span class="w-7 h-7 rounded-full {{ $index === 0 ? 'bg-red-50 text-red-600' : 'bg-gallery-surface-low text-zinc-400' }} flex items-center justify-center text-[10px] font-bold shrink-0">{{ $index + 1 }}</span>
                                <span class="text-[13px] font-bold text-black tracking-tight truncate flex-1 group-hover:opacity-60 transition-opacity">{{ $unit->name }}</span>
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest shrink-0">{{ number_format($unit->saved_by_users_count) }} {{ Str::plural('save', $unit->saved_by_users_count) }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="text-[11px] font-medium text-zinc-400">No units have been favorited yet.</li>
                    @endforelse
                </ol>
            </div>
        </div>
    </section>

    <!-- Recent Messaging Pipeline -->
    <section class="bg-white rounded-[40px] p-10 border border-gallery-outline/20 ambient-shadow overflow-hidden">
        <div class="flex justify-between items-center mb-10">
            <h3 class="text-[12px] font-bold text-black uppercase tracking-[0.4em]">Live Chat Pipeline</h3>
            <a href="{{ route('admin.messages') }}" class="text-[10px] font-bold text-black border-b-2 border-black pb-1 hover:opacity-60 transition-all uppercase tracking-widest">Open Message Center</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gallery-outline/10 text-zinc-400">
                        <th class="py-6 px-4 font-bold text-[10px] uppercase tracking-widest">Collector</th>
                        <th class="py-6 px-4 font-bold text-[10px] uppercase tracking-widest">Unit Context</th>
                        <th class="py-6 px-4 font-bold text-[10px] uppercase tracking-widest">Last Message</th>
                        <th class="py-6 px-4 font-bold text-[10px] uppercase tracking-widest">Received</th>
                        <th class="py-6 px-4 font-bold text-[10px] uppercase tracking-widest text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="text-[14px] text-black">
                    @forelse($recentInquiries as $msg)
                        <tr class="border-b border-gallery-outline/5 hover:bg-gallery-surface-low transition-colors duration-200 group">
                            <td class="py-6 px-4 font-bold">{{ $msg->user->name }}</td>
                            <td class="py-6 px-4 text-zinc-500 font-medium">{{ $msg->unit?->name ?? 'General' }}</td>
                            <td class="py-6 px-4">
                                <p class="text-xs text-zinc-400 truncate max-w-xs">{{ $msg->body }}</p>
                            </td>
                            <td class="py-6 px-4 text-zinc-400 font-medium">{{ $msg->created_at?->diffForHumans() }}</td>
                            <td class="py-6 px-4 text-right">
                                <a href="{{ route('admin.messages', ['user_id' => $msg->user_id, 'unit_id' => $msg->unit_id]) }}" class="text-zinc-300 group-hover:text-black transition-colors">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 inline" stroke="currentColor" stroke-width="2.5"><path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 px-4 text-center text-[11px] font-medium text-zinc-400">No messages yet — the pipeline is clear.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
