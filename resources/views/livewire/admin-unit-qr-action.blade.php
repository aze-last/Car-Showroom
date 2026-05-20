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
                    
                    @if (session('status'))
                        <div class="rounded-xl bg-emerald-50 p-4 text-sm font-bold text-emerald-600 border border-emerald-100 animate-showroom-fade-up">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (session('info'))
                        <div class="rounded-xl bg-zinc-50 p-4 text-sm font-bold text-zinc-600 border border-zinc-100 animate-showroom-fade-up">
                            {{ session('info') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="rounded-xl bg-red-50 p-4 text-sm font-bold text-red-600 border border-red-100 animate-showroom-fade-up">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($unit->status === Unit::STATUS_AVAILABLE)
                        <div class="space-y-3">
                            <label class="block">
                                <span class="mb-2 block text-sm font-medium text-slate-700">Reason (optional)</span>
                                <select wire:model="reason" class="admin-select">
                                    <option value="">No reason selected</option>
                                    <option value="Unit released to buyer">Unit released to buyer</option>
                                    <option value="Final sales confirmation">Final sales confirmation</option>
                                    <option value="Inventory reconciliation">Inventory reconciliation</option>
                                </select>
                            </label>
                            <button type="button" x-on:click="$flux.modal('confirm-sold').show()" class="admin-btn-danger w-full py-3 text-base">
                                Mark as SOLD
                            </button>
                        </div>
                    @else
                        <div class="space-y-3">
                            <label class="block">
                                <span class="mb-2 block text-sm font-medium text-slate-700">Reason (optional)</span>
                                <select wire:model="reason" class="admin-select">
                                    <option value="">No reason selected</option>
                                    <option value="Correction after verification">Correction after verification</option>
                                    <option value="Duplicate sale entry reversed">Duplicate sale entry reversed</option>
                                    <option value="Inventory reconciliation">Inventory reconciliation</option>
                                </select>
                            </label>
                            <button type="button" x-on:click="$flux.modal('confirm-available').show()" class="admin-btn-primary w-full bg-emerald-600 py-3 text-base hover:bg-emerald-500">
                                Mark as AVAILABLE
                            </button>
                        </div>
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

    {{-- Confirmation Modals --}}
    <flux:modal name="confirm-sold" class="min-w-[22rem] rounded-[32px] border-none shadow-2xl">
        <div class="space-y-6 p-4">
            <div>
                <h2 class="text-xs font-black uppercase tracking-[0.2em] text-red-600">Status Update</h2>
                <p class="mt-4 text-sm font-medium text-zinc-500 leading-relaxed">
                    Confirm setting this unit to <strong class="text-red-600">SOLD</strong>? This action will be recorded in the inventory logs.
                </p>
            </div>

            <div class="flex gap-3">
                <flux:modal.close>
                    <button type="button" class="admin-btn-secondary">Cancel</button>
                </flux:modal.close>
                <flux:spacer />
                <button type="button" wire:click="markAsSold" x-on:click="$flux.modal('confirm-sold').close()" class="admin-btn-danger">
                    Confirm Sold
                </button>
            </div>
        </div>
    </flux:modal>

    <flux:modal name="confirm-available" class="min-w-[22rem] rounded-[32px] border-none shadow-2xl">
        <div class="space-y-6 p-4">
            <div>
                <h2 class="text-xs font-black uppercase tracking-[0.2em] text-emerald-600">Status Update</h2>
                <p class="mt-4 text-sm font-medium text-zinc-500 leading-relaxed">
                    Confirm setting this unit to <strong class="text-emerald-600">AVAILABLE</strong>? The vehicle will be visible again in the public showroom.
                </p>
            </div>

            <div class="flex gap-3">
                <flux:modal.close>
                    <button type="button" class="admin-btn-secondary">Cancel</button>
                </flux:modal.close>
                <flux:spacer />
                <button type="button" wire:click="markAsAvailable" x-on:click="$flux.modal('confirm-available').close()" class="admin-btn-primary bg-emerald-600 hover:bg-emerald-500">
                    Confirm Available
                </button>
            </div>
        </div>
    </flux:modal>
</div>
