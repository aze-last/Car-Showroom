@php
    use App\Models\UnitStatusLog;
@endphp

<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 text-zinc-100">
        <section class="rounded-2xl border border-zinc-800 bg-zinc-900/70 p-5 shadow-lg shadow-black/20 sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-zinc-400">Inventory Operations</p>
                    <h1 class="mt-1 text-2xl font-semibold tracking-tight text-zinc-100 sm:text-3xl">Premium Dealership Admin Dashboard</h1>
                    <p class="mt-2 max-w-2xl text-sm text-zinc-400">Monitor inventory state, status updates, and activity logs in one place.</p>
                </div>

                @if ($canViewInventory)
                    <form action="{{ route('dashboard') }}" method="GET" class="w-full lg:max-w-sm">
                        <label for="dashboard-search" class="sr-only">Search units</label>
                        <div class="relative">
                            <svg viewBox="0 0 24 24" fill="none" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-500" stroke="currentColor" stroke-width="1.8">
                                <circle cx="11" cy="11" r="7"/>
                                <path d="M20 20L16.65 16.65" stroke-linecap="round"/>
                            </svg>
                            <input
                                id="dashboard-search"
                                name="q"
                                type="search"
                                value="{{ $searchQuery }}"
                                placeholder="Search units by name"
                                class="h-11 w-full rounded-xl border border-zinc-700 bg-zinc-900 px-10 text-sm text-zinc-100 placeholder:text-zinc-500 focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/20"
                            >
                        </div>
                    </form>
                @endif
            </div>
        </section>

        @if (! $canViewInventory)
            <section class="rounded-2xl border border-zinc-800 bg-zinc-900/70 p-6 text-sm text-zinc-300 shadow-lg shadow-black/20">
                Inventory insights are available for admin accounts only.
            </section>
        @else
            <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-2xl border border-zinc-800 bg-zinc-900/70 p-5 shadow-lg shadow-black/20 transition hover:-translate-y-0.5 hover:border-zinc-700">
                    <p class="text-xs font-medium uppercase tracking-wide text-zinc-400">Total Units</p>
                    <p class="mt-3 text-3xl font-semibold tracking-tight text-zinc-100">{{ number_format($totalUnits) }}</p>
                </article>

                <article class="rounded-2xl border border-emerald-700/35 bg-emerald-950/30 p-5 shadow-lg shadow-black/20 transition hover:-translate-y-0.5 hover:border-emerald-500/40">
                    <p class="text-xs font-medium uppercase tracking-wide text-emerald-300/80">Available</p>
                    <p class="mt-3 text-3xl font-semibold tracking-tight text-emerald-300">{{ number_format($availableUnits) }}</p>
                </article>

                <article class="rounded-2xl border border-red-700/35 bg-red-950/25 p-5 shadow-lg shadow-black/20 transition hover:-translate-y-0.5 hover:border-red-500/40">
                    <p class="text-xs font-medium uppercase tracking-wide text-red-300/80">Sold</p>
                    <p class="mt-3 text-3xl font-semibold tracking-tight text-red-300">{{ number_format($soldUnits) }}</p>
                </article>

                <article class="rounded-2xl border border-zinc-800 bg-zinc-900/70 p-5 shadow-lg shadow-black/20 transition hover:-translate-y-0.5 hover:border-zinc-700">
                    <p class="text-xs font-medium uppercase tracking-wide text-zinc-400">Added This Week</p>
                    <p class="mt-3 text-3xl font-semibold tracking-tight text-amber-300">{{ number_format($addedThisWeek) }}</p>
                </article>
            </section>

            <section class="grid grid-cols-1 gap-6 xl:grid-cols-[1.45fr_1fr]">
                <article class="rounded-2xl border border-zinc-800 bg-zinc-900/70 p-5 shadow-lg shadow-black/20 sm:p-6">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h2 class="text-lg font-semibold text-zinc-100">Quick Actions</h2>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <a href="{{ route('admin.units.create') }}" class="inline-flex items-center justify-center rounded-xl bg-amber-400 px-4 py-2.5 text-sm font-medium text-zinc-950 transition hover:bg-amber-300">
                            Add Unit
                        </a>
                        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center justify-center rounded-xl border border-zinc-700 bg-zinc-900 px-4 py-2.5 text-sm font-medium text-zinc-100 transition hover:border-zinc-600 hover:bg-zinc-800">
                            Manage Categories
                        </a>
                        <a href="{{ route('admin.units.index') }}" class="inline-flex items-center justify-center rounded-xl border border-zinc-700 bg-zinc-900 px-4 py-2.5 text-sm font-medium text-zinc-100 transition hover:border-zinc-600 hover:bg-zinc-800">
                            Generate QR
                        </a>
                        <a href="{{ route('admin.logs.index') }}" class="inline-flex items-center justify-center rounded-xl border border-zinc-700 bg-zinc-900 px-4 py-2.5 text-sm font-medium text-zinc-100 transition hover:border-zinc-600 hover:bg-zinc-800">
                            View Logs
                        </a>
                    </div>
                </article>

                <article class="rounded-2xl border border-zinc-800 bg-zinc-900/70 p-5 shadow-lg shadow-black/20 sm:p-6">
                    <h2 class="text-lg font-semibold text-zinc-100">Inventory Mix</h2>
                    @php
                        $mixTotal = max(1, $totalUnits);
                        $availablePercent = (int) round(($availableUnits / $mixTotal) * 100);
                        $soldPercent = (int) round(($soldUnits / $mixTotal) * 100);
                    @endphp

                    <div class="mt-4 space-y-4">
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm text-zinc-300">
                                <span>Available</span>
                                <span>{{ $availablePercent }}%</span>
                            </div>
                            <div class="h-2 rounded-full bg-zinc-800">
                                <div class="h-2 rounded-full bg-emerald-500" style="width: {{ $availablePercent }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm text-zinc-300">
                                <span>Sold</span>
                                <span>{{ $soldPercent }}%</span>
                            </div>
                            <div class="h-2 rounded-full bg-zinc-800">
                                <div class="h-2 rounded-full bg-red-500" style="width: {{ $soldPercent }}%"></div>
                            </div>
                        </div>
                    </div>
                </article>
            </section>

            <section class="rounded-2xl border border-zinc-800 bg-zinc-900/70 shadow-lg shadow-black/20">
                <div class="border-b border-zinc-800 px-5 py-4 sm:px-6">
                    <h2 class="text-lg font-semibold text-zinc-100">Recent Activity</h2>
                </div>

                <div class="max-h-[26rem] overflow-auto">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0 z-10 bg-zinc-950/95 text-zinc-400 backdrop-blur">
                            <tr>
                                <th class="px-5 py-3 text-left font-medium sm:px-6">Timestamp</th>
                                <th class="px-5 py-3 text-left font-medium">Unit</th>
                                <th class="px-5 py-3 text-left font-medium">Action</th>
                                <th class="px-5 py-3 text-left font-medium sm:px-6">User</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentLogs as $log)
                                @php
                                    $badgeClass = match ($log->action) {
                                        UnitStatusLog::ACTION_SET_SOLD => 'bg-red-600/90 text-white',
                                        UnitStatusLog::ACTION_SET_AVAILABLE => 'bg-emerald-600/90 text-white',
                                        default => 'bg-zinc-700 text-zinc-100',
                                    };
                                @endphp
                                <tr class="odd:bg-zinc-900/35 even:bg-zinc-900/10 hover:bg-zinc-800/55">
                                    <td class="px-5 py-3 text-zinc-300 sm:px-6">{{ $log->created_at?->format('Y-m-d H:i') }}</td>
                                    <td class="px-5 py-3 text-zinc-100">{{ $log->unit?->name ?? 'Unit #'.$log->unit_id }}</td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex rounded-md px-2 py-1 text-xs font-semibold {{ $badgeClass }}">{{ $log->action }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-zinc-300 sm:px-6">{{ $log->user?->name ?? 'User #'.$log->user_id }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-zinc-400 sm:px-6">
                                        No activity records yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @endif
    </div>
</x-layouts::app>
