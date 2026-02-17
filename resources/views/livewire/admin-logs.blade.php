@php
    use App\Models\UnitStatusLog;
@endphp

<section class="space-y-6">
    <article class="admin-card">
        <div class="admin-card-header">
            <h2 class="text-base font-semibold text-slate-900">Filters</h2>
            <button type="button" wire:click="clearFilters" class="admin-btn-secondary px-3 py-1.5 text-xs">Reset Filters</button>
        </div>
        <div class="admin-card-body">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <label class="block">
                    <span class="mb-2 block text-sm font-medium text-slate-700">Unit</span>
                    <select wire:model.live="unitId" class="admin-select">
                        <option value="">All units</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="mb-2 block text-sm font-medium text-slate-700">User</span>
                    <select wire:model.live="userId" class="admin-select">
                        <option value="">All users</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="mb-2 block text-sm font-medium text-slate-700">Action Type</span>
                    <select wire:model.live="actionType" class="admin-select">
                        <option value="">All actions</option>
                        @foreach ($actions as $action)
                            <option value="{{ $action }}">{{ $action }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="mb-2 block text-sm font-medium text-slate-700">From Date</span>
                    <input type="date" wire:model.live="fromDate" class="admin-input">
                </label>

                <label class="block">
                    <span class="mb-2 block text-sm font-medium text-slate-700">To Date</span>
                    <input type="date" wire:model.live="toDate" class="admin-input">
                </label>
            </div>
        </div>
    </article>

    <article class="admin-card overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-2 text-xs text-slate-500 sm:px-5">
            <span>Newest first</span>
            <span wire:loading wire:target="unitId,userId,actionType,fromDate,toDate,clearFilters">Updating...</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th scope="col" class="px-4 py-3 sm:px-5">Timestamp</th>
                        <th scope="col" class="px-4 py-3">Unit</th>
                        <th scope="col" class="px-4 py-3">Action</th>
                        <th scope="col" class="px-4 py-3">From → To</th>
                        <th scope="col" class="px-4 py-3">User</th>
                        <th scope="col" class="px-4 py-3 sm:px-5">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($logs as $log)
                        <tr wire:key="status-log-{{ $log->id }}" class="odd:bg-white even:bg-slate-50/40 hover:bg-slate-100/70">
                            <td class="px-4 py-3 sm:px-5 text-slate-600">{{ $log->created_at?->format('Y-m-d H:i:s') }}</td>
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
                            <td class="px-4 py-3 text-slate-600">
                                {{ $log->from_status ?? 'N/A' }} → {{ $log->to_status ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $log->user?->name ?? 'User #'.$log->user_id }}</td>
                            <td class="px-4 py-3 sm:px-5 font-mono text-xs text-slate-500">{{ $log->ip_address ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">
                                No logs found for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>

    <div class="flex justify-end">
        {{ $logs->links() }}
    </div>
</section>
