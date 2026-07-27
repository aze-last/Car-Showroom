@php
    use App\Models\Unit;
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="min-h-screen bg-gallery-background pb-20 animate-showroom-fade-up">
    @if($action === 'print')
        <!-- Dedicated Print Preview Layout -->
        <div class="flex flex-col items-center justify-center min-h-[80vh] pt-12 px-6">
            <div class="bg-white rounded-3xl lg:rounded-[40px] p-8 lg:p-12 ambient-shadow border border-gallery-outline/10 text-center flex flex-col items-center max-w-md w-full hover-lift transition-all duration-500">
                <h2 class="text-2xl font-bold tracking-tighter text-black mb-1">Print QR Label</h2>
                <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-8">Ready for inventory placement</p>
                
                {{-- QR Code visible on screen and print --}}
                <div id="print-qr-code" class="flex flex-col items-center justify-center gap-6 mb-8 mx-auto">
                    <div class="w-64 h-64 flex items-center justify-center bg-white p-4 rounded-3xl border border-gallery-outline/5 shadow-sm">
                        {!! $qrSvg !!}
                    </div>
                    <div class="text-center">
                        <h1 class="text-2xl font-bold text-black tracking-tight leading-tight">{{ $unit->name }}</h1>
                        <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest mt-1">{{ $unit->category?->name }} • {{ $unit->year }}</p>
                    </div>
                </div>

                <button onclick="window.print()" class="w-full h-14 bg-black text-white hover:bg-zinc-900 rounded-2xl flex items-center justify-center gap-2 text-[11px] font-bold uppercase tracking-[0.2em] transition-all">
                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="3"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2M6 14h12v8H6z"/></svg>
                    Print Label
                </button>
                
                <a href="{{ route('admin.units.index') }}" class="mt-6 text-[10px] font-bold uppercase tracking-[0.2em] text-zinc-400 hover:text-black transition-colors">
                    Back to Inventory
                </a>
            </div>
        </div>
    @else
        <!-- Original Mobile Terminal / Scan Successful Layout -->
        <!-- Top Bar (Back Action) -->
        <header class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-md border-b border-gallery-outline/10 h-20">
            <div class="flex justify-between items-center h-full px-6 sm:px-8 max-w-7xl mx-auto">
                <a href="{{ route('admin.units.index') }}" class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-zinc-400 hover:text-black transition-colors">
                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="3"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    <span class="hidden sm:inline">Exit Terminal</span>
                    <span class="sm:hidden">Exit</span>
                </a>
                <div class="text-[11px] sm:text-[12px] font-bold uppercase tracking-[0.3em] text-black">Mobile Terminal</div>
                <button onclick="window.print()" class="text-zinc-400 hover:text-black transition-colors">
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2.5"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2M6 14h12v8H6z"/></svg>
                </button>
            </div>
        </header>

        <main class="pt-28 sm:pt-32 px-4 sm:px-6 max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 lg:gap-12">
            <!-- Left Column: Scan Hero & Toggle -->
            <div class="lg:col-span-5 space-y-6 sm:space-y-8">
                <!-- Scan Success Card -->
                <div class="bg-white rounded-3xl lg:rounded-[40px] p-6 sm:p-8 lg:p-10 ambient-shadow border border-gallery-outline/10 text-center flex flex-col items-center group hover-lift transition-all duration-500">
                    <div class="print:hidden w-16 h-16 sm:w-20 sm:h-20 bg-emerald-50 rounded-full flex items-center justify-center mb-6 sm:mb-8 shadow-inner">
                        <svg viewBox="0 0 24 24" fill="none" class="h-8 w-8 sm:h-10 sm:w-10 text-emerald-600" stroke="currentColor" stroke-width="3"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14M22 4L12 14.01l-3-3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <h2 class="print:hidden text-2xl sm:text-3xl font-bold tracking-tighter text-black mb-2">Scan Successful</h2>
                    <p class="print:hidden text-xs sm:text-sm font-medium text-zinc-400 mb-8 sm:mb-10 px-2 sm:px-0">Vehicle authenticated and locked for your current curator session.</p>
                    
                    {{-- QR Code for Label Printing --}}
                    <div id="print-qr-code" class="hidden print:flex flex-col items-center justify-center gap-6 mb-8 mx-auto">
                        <div class="w-64 h-64 flex items-center justify-center">
                            {!! $qrSvg !!}
                        </div>
                        <div class="text-center">
                            <h1 class="text-2xl font-bold text-black tracking-tight">{{ $unit->name }}</h1>
                            <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest mt-1">{{ $unit->category?->name }} • {{ $unit->year }}</p>
                        </div>
                    </div>

                    <div class="w-full bg-gallery-surface-low rounded-2xl sm:rounded-3xl p-4 sm:p-6 flex items-center gap-4 sm:gap-6 text-left border border-gallery-outline/5 group-hover:bg-white transition-colors duration-500 print:border-none print:p-0">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl sm:rounded-2xl overflow-hidden bg-white shadow-sm flex-shrink-0">
                            @if($unit->mainImage)
                                <img src="{{ Storage::url($unit->mainImage->url) }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="min-w-0">
                            <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-1 truncate">#{{ substr($unit->public_id, -8) }}</div>
                            <h3 class="text-lg sm:text-xl font-bold text-black tracking-tight leading-tight truncate">{{ $unit->name }}</h3>
                            <p class="text-[10px] sm:text-[11px] font-bold text-zinc-400 uppercase tracking-widest mt-1 truncate">{{ $unit->category?->name }} • {{ $unit->year }}</p>
                        </div>
                    </div>
                </div>

                <!-- Status & Action Card -->
                <div class="bg-white rounded-3xl lg:rounded-[40px] p-6 sm:p-8 lg:p-10 ambient-shadow border border-gallery-outline/10 hover-lift transition-all duration-500">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 sm:gap-0 mb-6 sm:mb-8">
                        <div>
                            <h3 class="text-lg sm:text-xl font-bold text-black tracking-tight">Inventory Visibility</h3>
                            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mt-1">Control Public Listing</p>
                        </div>
                        
                        {{-- Status Badge --}}
                        @if($unit->status === Unit::STATUS_AVAILABLE)
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-100 text-emerald-600 text-[10px] font-bold uppercase tracking-widest w-fit">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse flex-shrink-0"></span>
                                Available
                            </div>
                        @else
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-zinc-100 border border-zinc-200 text-zinc-500 text-[10px] font-bold uppercase tracking-widest w-fit">
                                <span class="w-1.5 h-1.5 rounded-full bg-zinc-400 flex-shrink-0"></span>
                                Sold
                            </div>
                        @endif
                    </div>
                    
                    @if($unit->isAvailable())
                        <div class="bg-gallery-surface-low p-4 sm:p-6 rounded-2xl border border-gallery-outline/5 flex gap-3 sm:gap-4 items-start mb-6">
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-emerald-500 mt-0.5 flex-shrink-0" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                            <p class="text-[12px] sm:text-[13px] font-medium text-zinc-500 leading-relaxed">
                                This vehicle is currently <strong class="text-emerald-600 uppercase">Available</strong>. Fill in the buyer details below to lock the unit and mark it as sold.
                            </p>
                        </div>

                        <div class="space-y-6 pt-6 border-t border-gallery-outline/5">
                            <div>
                                <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-widest px-1 mb-3">Buyer Type</label>
                                <flux:radio.group wire:model.live="is_guest" variant="segmented" class="w-full mb-4">
                                    <flux:radio :value="false" label="Registered Collector" class="text-xs sm:text-sm" />
                                    <flux:radio :value="true" label="Guest Walk-in" class="text-xs sm:text-sm" />
                                </flux:radio.group>
                            </div>

                            @if(!$is_guest)
                                <div class="space-y-4">
                                    <div class="space-y-1">
                                        <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-widest px-1">Filter Collector</label>
                                        <flux:input wire:model.live.debounce.300ms="collector_search" placeholder="Search name or email..." class="w-full" />
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-widest px-1">Assign Collector</label>
                                        <flux:select wire:model="buyer_id" searchable placeholder="Select Collector...">
                                            @foreach($this->users as $user)
                                                <flux:select.option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</flux:select.option>
                                            @endforeach
                                        </flux:select>
                                    </div>
                                </div>
                            @else
                                <div class="space-y-4 bg-gallery-surface-low p-4 sm:p-6 rounded-2xl border border-gallery-outline/5">
                                    <div class="space-y-1">
                                        <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-widest px-1">Guest Name</label>
                                        <flux:input wire:model="guest_name" placeholder="John Doe" class="w-full" />
                                    </div>
                                    <div class="space-y-1">
                                        <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-widest px-1">Contact Number</label>
                                        <flux:input wire:model="guest_contact" placeholder="+63 912 345 6789" class="w-full" />
                                    </div>
                                    <div class="space-y-1">
                                        <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-widest px-1">Handover Photo</label>
                                        <flux:input type="file" wire:model="handover_image" accept="image/*" />
                                        @if ($handover_image)
                                            <div class="mt-4 w-full sm:w-32 h-40 sm:h-32 rounded-xl overflow-hidden border border-gallery-outline/10 shadow-sm">
                                                <img src="{{ $handover_image->temporaryUrl() }}" class="w-full h-full object-cover">
                                            </div>
                                        @endif
                                        <div wire:loading wire:target="handover_image" class="text-xs font-medium text-emerald-500 mt-2">Uploading and optimizing image...</div>
                                    </div>
                                </div>
                            @endif

                            <button 
                                wire:click="markAsSold"
                                wire:loading.attr="disabled"
                                class="w-full h-14 mt-4 bg-black text-white hover:bg-zinc-900 rounded-2xl flex items-center justify-center gap-2 text-[11px] font-bold uppercase tracking-[0.2em] transition-all focus:ring-4 focus:ring-zinc-200"
                            >
                                <span wire:loading wire:target="markAsSold" class="h-4 w-4 animate-spin rounded-full border-2 border-white/20 border-t-white"></span>
                                <svg wire:loading.remove wire:target="markAsSold" viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="3"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14M22 4L12 14.01l-3-3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Confirm & Mark as Sold
                            </button>
                        </div>
                    @else
                        <div class="bg-gallery-surface-low p-4 sm:p-6 rounded-2xl border border-gallery-outline/5 flex gap-3 sm:gap-4 items-start mb-6">
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-zinc-400 mt-0.5 flex-shrink-0" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><circle cx="12" cy="12" r="3"/></svg>
                            <p class="text-[12px] sm:text-[13px] font-medium text-zinc-500 leading-relaxed">
                                This vehicle was marked as <strong class="text-black uppercase">Sold</strong>. Reverting it to Available will re-list it on the public catalog.
                            </p>
                        </div>
                        
                        <div class="pt-6 border-t border-gallery-outline/5">
                            <div class="space-y-1 mb-6">
                                <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-widest px-1">Reason for Revert (Optional)</label>
                                <flux:input wire:model="reason" placeholder="e.g. Buyer financing fell through" class="w-full" />
                            </div>

                            <button 
                                wire:click="markAsAvailable"
                                wire:loading.attr="disabled"
                                class="w-full h-14 bg-zinc-100 text-zinc-900 hover:bg-zinc-200 border border-zinc-200 rounded-2xl flex items-center justify-center gap-2 text-[11px] font-bold uppercase tracking-[0.2em] transition-all"
                            >
                                <span wire:loading wire:target="markAsAvailable" class="h-4 w-4 animate-spin rounded-full border-2 border-zinc-400/20 border-t-zinc-600"></span>
                                <svg wire:loading.remove wire:target="markAsAvailable" viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="3"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                                Revert to Available
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Audit & Lock -->
            <div class="lg:col-span-7 space-y-6 sm:space-y-8">
                <!-- Concurrency Alert -->
                <div class="bg-amber-50 border border-amber-100 rounded-2xl sm:rounded-3xl p-4 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div class="h-10 w-10 flex-shrink-0 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><circle cx="12" cy="12" r="3"/></svg>
                        </div>
                        <div>
                            <p class="text-xs sm:text-[13px] font-bold text-amber-900 leading-none">Session Lock Active</p>
                            <p class="text-[10px] font-bold text-amber-600/60 uppercase tracking-widest mt-1">Exclusive Curator Access</p>
                        </div>
                    </div>
                    <span class="text-[10px] sm:text-[11px] font-bold text-amber-600 bg-white px-3 sm:px-4 py-1.5 rounded-full shadow-sm whitespace-nowrap">14:59 Remaining</span>
                </div>

                <!-- Workflow Bento Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-8">
                    <div class="bg-white rounded-3xl lg:rounded-[40px] p-5 sm:p-6 lg:p-8 border border-gallery-outline/10 ambient-shadow hover-lift transition-all flex flex-row sm:flex-col items-center sm:items-start text-left gap-4 sm:gap-0">
                        <div class="h-10 w-10 flex-shrink-0 rounded-full bg-gallery-surface-low flex items-center justify-center text-black sm:mb-6">
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14M22 4L12 14.01l-3-3"/></svg>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-1">Inspection</div>
                            <div class="text-xl sm:text-2xl lg:text-3xl font-bold text-black tracking-tight">Passed</div>
                            <div class="mt-1 sm:mt-4 lg:mt-6 flex items-center gap-2">
                                <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Verified Today</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-3xl lg:rounded-[40px] p-5 sm:p-6 lg:p-8 border border-gallery-outline/10 ambient-shadow hover-lift transition-all flex flex-row sm:flex-col items-center sm:items-start text-left gap-4 sm:gap-0">
                        <div class="h-10 w-10 flex-shrink-0 rounded-full bg-gallery-surface-low flex items-center justify-center text-black sm:mb-6">
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2.5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-1">Pricing Tier</div>
                            <div class="text-xl sm:text-2xl lg:text-3xl font-bold text-black tracking-tight">Premium</div>
                            <div class="mt-1 sm:mt-4 lg:mt-6 flex items-center gap-2">
                                <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-zinc-200"></div>
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Fixed Portfolio</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- History List -->
                <div class="bg-white rounded-3xl lg:rounded-[40px] p-6 sm:p-8 lg:p-10 ambient-shadow border border-gallery-outline/10">
                    <h3 class="text-[12px] font-bold text-black uppercase tracking-[0.4em] mb-8 pb-4 border-b border-gallery-outline/10">Status Transition Trail</h3>
                    
                    <div class="relative space-y-8 sm:space-y-10 before:absolute before:left-[15px] sm:before:left-[15px] before:top-2 before:bottom-2 before:w-px before:bg-gallery-outline/10">
                        @forelse($unit->statusLogs()->take(5)->get() as $log)
                            <div class="relative flex gap-5 sm:gap-8 group">
                                <div class="w-8 h-8 rounded-full {{ $log->action === 'SET_AVAILABLE' ? 'bg-emerald-50 text-emerald-600' : 'bg-black text-white' }} flex items-center justify-center flex-shrink-0 z-10 ring-8 ring-white shadow-sm transition-transform group-hover:scale-110">
                                    @if($log->action === 'SET_AVAILABLE')
                                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                    @else
                                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="3"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                    @endif
                                </div>
                                <div class="flex-grow pt-0.5 sm:pt-1">
                                    <div class="flex flex-col sm:flex-row justify-between items-start gap-1 sm:gap-0">
                                        <h4 class="text-[13px] sm:text-sm font-bold text-black uppercase tracking-tight">Status: {{ str_replace('SET_', '', $log->action) }}</h4>
                                        <span class="text-[10px] font-bold text-zinc-300 uppercase tracking-widest">{{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-[11px] sm:text-[12px] font-medium text-zinc-400 mt-1 sm:mt-1.5 leading-snug">Logged via Mobile Terminal by <span class="text-black">{{ $log->user?->name ?? 'Curator' }}</span></p>
                                    @if($log->reason)
                                        <div class="mt-3 p-3 sm:p-4 rounded-xl bg-gallery-surface-low border border-gallery-outline/5 text-[11px] font-medium text-zinc-500 italic">
                                            "{{ $log->reason }}"
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center opacity-30">
                                <span class="text-[10px] font-bold uppercase tracking-widest">No history recorded</span>
                            </div>
                        @endforelse
                    </div>

                    <a href="{{ route('admin.logs.index') }}" class="w-full h-14 mt-10 border border-gallery-outline/30 rounded-2xl flex items-center justify-center text-[10px] font-bold text-zinc-400 hover:text-black hover:border-black transition-all uppercase tracking-[0.2em]">
                        Access Full Audit Log
                    </a>
                </div>
            </div>
        </main>
    @endif

    <style>
        @media print {
            @page {
                size: auto;
                margin: 0;
            }
            body {
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            /* Hide all screen elements */
            body * {
                visibility: hidden !important;
            }
            /* Show only the QR code wrapper and its descendants */
            #print-qr-code, #print-qr-code * {
                visibility: visible !important;
            }
            /* Center the QR code in the print page */
            #print-qr-code {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                height: 100vh !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
                align-items: center !important;
                page-break-inside: avoid !important;
                margin: 0 !important;
                padding: 20px !important;
                gap: 24px !important;
            }
            #print-qr-code svg {
                width: 60vmin !important;
                height: 60vmin !important;
                max-width: 300px !important;
                max-height: 300px !important;
            }
        }
    </style>
</div>