@php
    use App\Models\UnitStatusLog;

    $soldPercentage = max(0, 100 - $availablePercentage);
@endphp

<section class="space-y-6">
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <article class="admin-card transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="admin-card-body flex items-start justify-between">
                <div>
                    <p class="text-sm text-slate-500">Total Units</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ number_format($totalUnits) }}</p>
                </div>
                <span class="rounded-lg bg-slate-100 p-2 text-slate-600">
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 8.5L12 4L20 8.5V15.5L12 20L4 15.5V8.5Z" stroke-linejoin="round"/>
                    </svg>
                </span>
            </div>
        </article>

        <article class="admin-card transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="admin-card-body flex items-start justify-between">
                <div>
                    <p class="text-sm text-slate-500">Available Units</p>
                    <p class="mt-2 text-3xl font-semibold text-emerald-600">{{ number_format($availableUnits) }}</p>
                </div>
                <span class="rounded-lg bg-emerald-100 p-2 text-emerald-700">
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="1.8">
                        <path d="M5 12L10 17L19 8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            </div>
        </article>

        <article class="admin-card transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="admin-card-body flex items-start justify-between">
                <div>
                    <p class="text-sm text-slate-500">Sold Units</p>
                    <p class="mt-2 text-3xl font-semibold text-red-600">{{ number_format($soldUnits) }}</p>
                </div>
                <span class="rounded-lg bg-red-100 p-2 text-red-700">
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="1.8">
                        <path d="M6 6L18 18M18 6L6 18" stroke-linecap="round"/>
                    </svg>
                </span>
            </div>
        </article>

        <article class="admin-card transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="admin-card-body flex items-start justify-between">
                <div>
                    <p class="text-sm text-slate-500">Added This Week</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ number_format($addedThisWeek) }}</p>
                </div>
                <span class="rounded-lg bg-slate-100 p-2 text-slate-600">
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="1.8">
                        <path d="M7 4V7M17 4V7M4 10H20M6 20H18A2 2 0 0 0 20 18V8A2 2 0 0 0 18 6H6A2 2 0 0 0 4 8V18A2 2 0 0 0 6 20Z" stroke-linecap="round"/>
                    </svg>
                </span>
            </div>
        </article>
    </div>

    <article class="admin-card">
        <div class="admin-card-header">
            <h2 class="text-base font-semibold text-slate-900">Quick Actions</h2>
        </div>
        <div class="admin-card-body">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('admin.units.create') }}" class="admin-btn-primary w-full justify-start">
                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 5V19M5 12H19" stroke-linecap="round"/>
                    </svg>
                    Add New Unit
                </a>
                <a href="{{ route('admin.categories.index') }}" class="admin-btn-secondary w-full justify-start">
                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 7H20M4 12H20M4 17H14" stroke-linecap="round"/>
                    </svg>
                    Manage Categories
                </a>
                <a href="{{ route('admin.units.index') }}" class="admin-btn-secondary w-full justify-start">
                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 6H20V18H4V6Z" stroke-linejoin="round"/>
                        <path d="M8 10L11 13L16 8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Generate QR Codes
                </a>
                <a href="{{ route('admin.logs.index') }}" class="admin-btn-secondary w-full justify-start">
                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="1.8">
                        <path d="M6 6H18V18H6V6Z" stroke-linejoin="round"/>
                        <path d="M9 10H15M9 14H13" stroke-linecap="round"/>
                    </svg>
                    View Logs
                </a>
            </div>
        </div>
    </article>

    <div class="grid gap-6 xl:grid-cols-[1.6fr_1fr]">
        <article class="admin-card">
            <div class="admin-card-header">
                <h2 class="text-base font-semibold text-slate-900">Recent Activity</h2>
                <a href="{{ route('admin.logs.index') }}" class="admin-btn-secondary px-3 py-1.5 text-xs">View All Logs</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th scope="col" class="px-4 py-3">Timestamp</th>
                            <th scope="col" class="px-4 py-3">Unit Name</th>
                            <th scope="col" class="px-4 py-3">Action</th>
                            <th scope="col" class="px-4 py-3">Performed By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recentLogs as $log)
                            <tr>
                                <td class="px-4 py-3 text-slate-600">{{ $log->created_at?->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $log->unit?->name ?? 'Unit #'.$log->unit_id }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $badgeClass = match ($log->action) {
                                            UnitStatusLog::ACTION_SET_AVAILABLE => 'admin-badge admin-badge-available',
                                            UnitStatusLog::ACTION_SET_SOLD => 'admin-badge admin-badge-sold',
                                            default => 'admin-badge bg-slate-100 text-slate-700',
                                        };
                                    @endphp
                                    <span class="{{ $badgeClass }}">{{ $log->action }}</span>
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $log->user?->name ?? 'User #'.$log->user_id }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">
                                    No recent activity yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="admin-card">
            <div class="admin-card-header">
                <h2 class="text-base font-semibold text-slate-900">Inventory Status</h2>
            </div>
            <div class="admin-card-body">
                <div class="mx-auto flex max-w-xs flex-col items-center gap-4">
                    <div
                        class="relative h-48 w-48 rounded-full"
                        style="background: conic-gradient(#16a34a 0 {{ $availablePercentage }}%, #dc2626 {{ $availablePercentage }}% 100%);"
                        aria-label="Available {{ $availablePercentage }} percent, Sold {{ $soldPercentage }} percent"
                        role="img"
                    >
                        <div class="absolute inset-[22%] flex items-center justify-center rounded-full bg-white shadow-inner">
                            <div class="text-center">
                                <p class="text-2xl font-semibold text-slate-900">{{ number_format($totalUnits) }}</p>
                                <p class="text-xs uppercase tracking-wide text-slate-500">Units</p>
                            </div>
                        </div>
                    </div>

                    <ul class="w-full space-y-2 text-sm">
                        <li class="flex items-center justify-between rounded-md border border-slate-200 px-3 py-2">
                            <span class="inline-flex items-center gap-2 text-slate-600">
                                <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                                Available
                            </span>
                            <span class="font-semibold text-slate-900">{{ number_format($availableUnits) }} ({{ $availablePercentage }}%)</span>
                        </li>
                        <li class="flex items-center justify-between rounded-md border border-slate-200 px-3 py-2">
                            <span class="inline-flex items-center gap-2 text-slate-600">
                                <span class="h-2.5 w-2.5 rounded-full bg-red-500"></span>
                                Sold
                            </span>
                            <span class="font-semibold text-slate-900">{{ number_format($soldUnits) }} ({{ $soldPercentage }}%)</span>
                        </li>
                    </ul>
                </div>
            </div>
        </article>
    </div>
</section>
