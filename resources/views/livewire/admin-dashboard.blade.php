@php
    use App\Models\UnitStatusLog;

    $soldPercentage = max(0, 100 - $availablePercentage);
@endphp

<section class="space-y-10">
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
        <article class="admin-card group hover:-translate-y-1 transition-all duration-300">
            <div class="admin-card-body flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">Total Inventory</p>
                    <p class="mt-4 text-4xl font-black tracking-tight text-zinc-900">{{ number_format($totalUnits) }}</p>
                </div>
                <span class="rounded-xl bg-zinc-50 p-3 text-zinc-400 group-hover:bg-zinc-900 group-hover:text-white transition-colors duration-300 shadow-inner">
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2.5">
                        <path d="M4 8.5L12 4L20 8.5V15.5L12 20L4 15.5V8.5Z" stroke-linejoin="round"/>
                    </svg>
                </span>
            </div>
        </article>

        <article class="admin-card group hover:-translate-y-1 transition-all duration-300 border-emerald-50 shadow-emerald-500/5">
            <div class="admin-card-body flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-emerald-600/60">Available Units</p>
                    <p class="mt-4 text-4xl font-black tracking-tight text-emerald-600">{{ number_format($availableUnits) }}</p>
                </div>
                <span class="rounded-xl bg-emerald-50 p-3 text-emerald-500 group-hover:bg-emerald-500 group-hover:text-white transition-colors duration-300 shadow-inner">
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="3">
                        <path d="M5 12L10 17L19 8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            </div>
        </article>

        <article class="admin-card group hover:-translate-y-1 transition-all duration-300 border-zinc-100">
            <div class="admin-card-body flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">Units Sold</p>
                    <p class="mt-4 text-4xl font-black tracking-tight text-zinc-900">{{ number_format($soldUnits) }}</p>
                </div>
                <span class="rounded-xl bg-zinc-50 p-3 text-zinc-300 group-hover:bg-zinc-400 group-hover:text-white transition-colors duration-300 shadow-inner">
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="3">
                        <path d="M6 6L18 18M18 6L6 18" stroke-linecap="round"/>
                    </svg>
                </span>
            </div>
        </article>

        <article class="admin-card group hover:-translate-y-1 transition-all duration-300 border-blue-50 shadow-blue-500/5">
            <div class="admin-card-body flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-600/60">Weekly Growth</p>
                    <p class="mt-4 text-4xl font-black tracking-tight text-blue-600">+{{ number_format($addedThisWeek) }}</p>
                </div>
                <span class="rounded-xl bg-blue-50 p-3 text-blue-500 group-hover:bg-blue-500 group-hover:text-white transition-colors duration-300 shadow-inner">
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2.5">
                        <path d="M7 4V7M17 4V7M4 10H20M6 20H18A2 2 0 0 0 20 18V8A2 2 0 0 0 18 6H6A2 2 0 0 0 4 8V18A2 2 0 0 0 6 20Z" stroke-linecap="round"/>
                    </svg>
                </span>
            </div>
        </article>
    </div>

    <div class="grid gap-10 xl:grid-cols-[1fr_auto]">
        <div class="space-y-8">
            <header class="flex items-end justify-between">
                <div>
                    <h2 class="text-xs font-black uppercase tracking-[0.4em] text-zinc-900">Recent Activity</h2>
                    <div class="mt-2 h-1 w-8 bg-zinc-900"></div>
                </div>
                <a href="{{ route('admin.logs.index') }}" class="text-[10px] font-black uppercase tracking-widest text-zinc-400 hover:text-zinc-900 transition-colors">View History</a>
            </header>

            <div class="admin-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-zinc-50/50 text-left text-[10px] font-black uppercase tracking-widest text-zinc-400 border-b border-zinc-100">
                            <tr>
                                <th scope="col" class="px-8 py-4">Timestamp</th>
                                <th scope="col" class="px-8 py-4">Unit Name</th>
                                <th scope="col" class="px-8 py-4">Action</th>
                                <th scope="col" class="px-8 py-4">Operator</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-50">
                            @forelse ($recentLogs as $log)
                                <tr class="hover:bg-zinc-50/50 transition-colors">
                                    <td class="px-8 py-5 text-xs text-zinc-500 font-medium tracking-tight">{{ $log->created_at?->format('M d, H:i') }}</td>
                                    <td class="px-8 py-5 font-bold text-zinc-900">{{ $log->unit?->name ?? 'Unit #'.$log->unit_id }}</td>
                                    <td class="px-8 py-5">
                                        @php
                                            $badgeClass = match ($log->action) {
                                                UnitStatusLog::ACTION_SET_AVAILABLE => 'admin-badge admin-badge-available',
                                                UnitStatusLog::ACTION_SET_SOLD => 'admin-badge admin-badge-sold',
                                                default => 'admin-badge bg-zinc-100 text-zinc-500',
                                            };
                                        @endphp
                                        <span class="{{ $badgeClass }}">{{ $log->action }}</span>
                                    </td>
                                    <td class="px-8 py-5 text-xs text-zinc-500 font-bold uppercase tracking-widest">{{ $log->user?->name ?? 'System' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-16 text-center">
                                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-300">No recent activity detected</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="w-full xl:w-[320px] space-y-8">
            <header>
                <h2 class="text-xs font-black uppercase tracking-[0.4em] text-zinc-900">Inventory Split</h2>
                <div class="mt-2 h-1 w-8 bg-zinc-900"></div>
            </header>

            <article class="admin-card">
                <div class="admin-card-body">
                    <div class="mx-auto flex flex-col items-center gap-10">
                        <div
                            class="relative h-44 w-44 rounded-full shadow-2xl shadow-zinc-200"
                            style="background: conic-gradient(#10b981 0 {{ $availablePercentage }}%, #e5e7eb {{ $availablePercentage }}% 100%);"
                            aria-label="Available {{ $availablePercentage }} percent"
                            role="img"
                        >
                            <div class="absolute inset-[15%] flex items-center justify-center rounded-full bg-white border border-zinc-50">
                                <div class="text-center">
                                    <p class="text-3xl font-black tracking-tighter text-zinc-900">{{ $availablePercentage }}%</p>
                                    <p class="text-[8px] font-black uppercase tracking-widest text-zinc-400">Available</p>
                                </div>
                            </div>
                        </div>

                        <ul class="w-full space-y-4">
                            <li class="flex items-center justify-between p-4 rounded-2xl bg-zinc-50/50 border border-zinc-100">
                                <span class="flex items-center gap-3 text-[10px] font-black uppercase tracking-widest text-zinc-500">
                                    <span class="h-2 w-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                                    Available
                                </span>
                                <span class="text-sm font-black text-zinc-900">{{ number_format($availableUnits) }}</span>
                            </li>
                            <li class="flex items-center justify-between p-4 rounded-2xl bg-zinc-50/50 border border-zinc-100">
                                <span class="flex items-center gap-3 text-[10px] font-black uppercase tracking-widest text-zinc-500">
                                    <span class="h-2 w-2 rounded-full bg-zinc-300"></span>
                                    Sold
                                </span>
                                <span class="text-sm font-black text-zinc-900">{{ number_format($soldUnits) }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </article>
        </div>
    </div>

    <article class="admin-card bg-zinc-900 border-none shadow-2xl shadow-zinc-900/20">
        <div class="admin-card-body">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h2 class="text-base font-black text-white uppercase tracking-[0.2em]">Ready for updates?</h2>
                    <p class="mt-1 text-sm text-zinc-400 font-medium">Sync with external catalogs or manage your inquiries instantly.</p>
                </div>
                <div class="flex flex-wrap items-center gap-4">
                    <a href="{{ route('admin.units.create') }}" class="flex h-12 items-center justify-center gap-2 rounded-xl bg-white px-6 text-xs font-black uppercase tracking-widest text-zinc-900 hover:bg-zinc-100 transition-all">
                        New Unit
                    </a>
                    <a href="{{ route('admin.inquiries.index') }}" class="flex h-12 items-center justify-center gap-2 rounded-xl bg-zinc-800 px-6 text-xs font-black uppercase tracking-widest text-zinc-300 hover:bg-zinc-700 transition-all">
                        Inquiries
                    </a>
                </div>
            </div>
        </div>
    </article>
</section>
