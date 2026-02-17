@php
    use App\Models\Unit;
    use Illuminate\Support\Str;
@endphp

<div>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>

    <section class="mx-auto w-full max-w-2xl space-y-5">
        <div class="no-print flex flex-wrap items-center justify-between gap-2">
            <a href="{{ route('admin.units.edit', $unit) }}" class="admin-btn-secondary px-3 py-1.5 text-xs">
                Back to Unit
            </a>
            <button type="button" onclick="window.print()" class="admin-btn-secondary px-3 py-1.5 text-xs">
                Print Layout
            </button>
        </div>

        <article class="admin-card">
            <div class="admin-card-body space-y-5">
                <header class="text-center">
                    <h2 class="text-2xl font-semibold text-slate-900">{{ $unit->name }}</h2>
                    <p class="mt-1 text-sm text-slate-500">Category: {{ $unit->category?->name ?? 'Uncategorized' }}</p>
                    <div class="mt-3">
                        <span class="{{ $unit->status === Unit::STATUS_AVAILABLE ? 'admin-badge admin-badge-available text-sm px-3 py-1.5' : 'admin-badge admin-badge-sold text-sm px-3 py-1.5' }}">
                            {{ $unit->status }}
                        </span>
                    </div>
                </header>

                <div class="mx-auto max-w-[280px] rounded-lg border border-slate-200 bg-white p-2">
                    {!! $qrSvg !!}
                </div>

                <section class="no-print space-y-3">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-600">Confirm Action</h3>
                    @if ($unit->status === Unit::STATUS_AVAILABLE)
                        <form method="POST" action="{{ route('admin.units.set-sold', $unit) }}" onsubmit="return confirm('Mark this unit as SOLD?')" data-disable-on-submit class="space-y-3">
                            @csrf
                            <input type="hidden" name="request_id" value="{{ (string) Str::uuid() }}">
                            <label class="block">
                                <span class="mb-2 block text-sm font-medium text-slate-700">Reason (optional)</span>
                                <select name="reason" class="admin-select">
                                    <option value="">No reason selected</option>
                                    <option value="Unit released to buyer">Unit released to buyer</option>
                                    <option value="Final sales confirmation">Final sales confirmation</option>
                                    <option value="Inventory reconciliation">Inventory reconciliation</option>
                                </select>
                            </label>
                            <button type="submit" class="admin-btn-danger w-full py-3 text-base">
                                Mark as SOLD
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.units.set-available', $unit) }}" onsubmit="return confirm('Mark this unit as AVAILABLE?')" data-disable-on-submit class="space-y-3">
                            @csrf
                            <input type="hidden" name="request_id" value="{{ (string) Str::uuid() }}">
                            <label class="block">
                                <span class="mb-2 block text-sm font-medium text-slate-700">Reason (optional)</span>
                                <select name="reason" class="admin-select">
                                    <option value="">No reason selected</option>
                                    <option value="Correction after verification">Correction after verification</option>
                                    <option value="Duplicate sale entry reversed">Duplicate sale entry reversed</option>
                                    <option value="Inventory reconciliation">Inventory reconciliation</option>
                                </select>
                            </label>
                            <button type="submit" class="admin-btn-primary w-full bg-emerald-600 py-3 text-base hover:bg-emerald-500">
                                Mark as AVAILABLE
                            </button>
                        </form>
                    @endif
                </section>

                <footer class="rounded-md border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                    @if ($lastLog)
                        <p>
                            Last changed by
                            <span class="font-semibold text-slate-800">{{ $lastLog->user?->name ?? 'Unknown User' }}</span>
                            on
                            <span class="font-semibold text-slate-800">{{ $lastLog->created_at?->format('Y-m-d H:i:s') }}</span>
                        </p>
                    @else
                        <p>No status changes logged yet.</p>
                    @endif
                </footer>
            </div>
        </article>
    </section>
</div>
