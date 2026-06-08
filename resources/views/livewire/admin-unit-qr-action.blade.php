@php
    use App\Models\Unit;
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="min-h-screen bg-gallery-background pb-20 animate-showroom-fade-up">
    <!-- Top Bar (Back Action) -->
    <header class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-md border-b border-gallery-outline/10 h-20">
        <div class="flex justify-between items-center h-full px-8 max-w-7xl mx-auto">
            <a href="{{ route('admin.units.index') }}" class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-zinc-400 hover:text-black transition-colors">
                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="3"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Exit Terminal
            </a>
            <div class="text-[12px] font-bold uppercase tracking-[0.3em] text-black">Mobile Terminal</div>
            <button onclick="window.print()" class="text-zinc-400 hover:text-black transition-colors">
                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2.5"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2M6 14h12v8H6z"/></svg>
            </button>
        </div>
    </header>

    <main class="pt-32 px-6 max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12">
        <!-- Left Column: Scan Hero & Toggle -->
        <div class="lg:col-span-5 space-y-8">
            <!-- Scan Success Card -->
            <div class="bg-white rounded-[40px] p-10 ambient-shadow border border-gallery-outline/10 text-center flex flex-col items-center group hover-lift transition-all duration-500">
                <div class="print:hidden w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mb-8 shadow-inner">
                    <svg viewBox="0 0 24 24" fill="none" class="h-10 w-10 text-emerald-600" stroke="currentColor" stroke-width="3"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14M22 4L12 14.01l-3-3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h2 class="print:hidden text-3xl font-bold tracking-tighter text-black mb-2">Scan Successful</h2>
                <p class="print:hidden text-sm font-medium text-zinc-400 mb-10">Vehicle authenticated and locked for your current curator session.</p>
                
                {{-- QR Code for Label Printing --}}
                <div id="print-qr-code" class="hidden print:flex mb-8 w-48 h-48 mx-auto">
                    {!! $qrSvg !!}
                </div>

                <div class="w-full bg-gallery-surface-low rounded-3xl p-6 flex items-center gap-6 text-left border border-gallery-outline/5 group-hover:bg-white transition-colors duration-500 print:border-none print:p-0">
                    <div class="w-20 h-20 rounded-2xl overflow-hidden bg-white shadow-sm flex-shrink-0">
                        @if($unit->mainImage)
                            <img src="{{ Storage::url($unit->mainImage->url) }}" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-1">#{{ substr($unit->public_id, -8) }}</div>
                        <h3 class="text-xl font-bold text-black tracking-tight leading-tight">{{ $unit->name }}</h3>
                        <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest mt-1">{{ $unit->category?->name }} • {{ $unit->year }}</p>
                    </div>
                </div>
            </div>

            <!-- Availability Toggle Card -->
            <div class="bg-white rounded-[40px] p-10 ambient-shadow border border-gallery-outline/10 hover-lift transition-all duration-500">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h3 class="text-lg font-bold text-black tracking-tight">Inventory Visibility</h3>
                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mt-1">Control Public Listing</p>
                    </div>
                    
                    {{-- Premium Toggle Switch --}}
                    <button 
                        wire:click="{{ $unit->status === Unit::STATUS_AVAILABLE ? 'markAsSold' : 'markAsAvailable' }}"
                        class="relative inline-flex h-10 w-20 flex-shrink-0 cursor-pointer items-center rounded-full transition-colors duration-500 focus:outline-none {{ $unit->status === Unit::STATUS_AVAILABLE ? 'bg-emerald-500' : 'bg-zinc-200' }}"
                    >
                        <span class="sr-only">Toggle Status</span>
                        <span 
                            class="inline-block h-8 w-8 transform rounded-full bg-white shadow-xl transition-transform duration-500 {{ $unit->status === Unit::STATUS_AVAILABLE ? 'translate-x-11' : 'translate-x-1' }}"
                        ></span>
                    </button>
                </div>
                
                <div class="bg-gallery-surface-low p-6 rounded-2xl border border-gallery-outline/5 flex gap-4 items-start">
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-zinc-400 mt-0.5" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    <p class="text-[13px] font-medium text-zinc-500 leading-relaxed">
                        Setting this vehicle to <strong class="text-black uppercase">Available</strong> will immediately push the listing to the public Catalog and third-party syndication.
                    </p>
                </div>

                @if($unit->isAvailable())
                <div class="space-y-6 pt-6 border-t border-gallery-outline/5">
                    <div>
                        <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-widest px-1 mb-3">Buyer Type</label>
                        <flux:radio.group wire:model.live="is_guest" variant="segmented" class="w-full mb-4">
                            <flux:radio value="0" label="Registered Collector" />
                            <flux:radio value="1" label="Guest Walk-in" />
                        </flux:radio.group>
                    </div>

                    @if(!$is_guest)
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-widest px-1">Assign Collector</label>
                            <flux:select wire:model="buyer_id" searchable placeholder="Select Collector...">
                                @foreach($users as $user)
                                    <flux:select.option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>
                    @else
                        <div class="space-y-4 bg-gallery-surface-low p-6 rounded-2xl border border-gallery-outline/5">
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
                                    <div class="mt-4 w-32 h-32 rounded-xl overflow-hidden border border-gallery-outline/10 shadow-sm">
                                        <img src="{{ $handover_image->temporaryUrl() }}" class="w-full h-full object-cover">
                                    </div>
                                @endif
                                <div wire:loading wire:target="handover_image" class="text-xs font-medium text-zinc-500 mt-2">Uploading...</div>
                            </div>
                        </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Audit & Lock -->
        <div class="lg:col-span-7 space-y-8">
            <!-- Concurrency Alert -->
            <div class="bg-amber-50 border border-amber-100 rounded-3xl p-6 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="h-10 w-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                        <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><circle cx="12" cy="12" r="3"/></svg>
                    </div>
                    <div>
                        <p class="text-[13px] font-bold text-amber-900 leading-none">Session Lock Active</p>
                        <p class="text-[10px] font-bold text-amber-600/60 uppercase tracking-widest mt-1">Exclusive Curator Access</p>
                    </div>
                </div>
                <span class="text-[11px] font-bold text-amber-600 bg-white px-4 py-1.5 rounded-full shadow-sm">14:59 Remaining</span>
            </div>

            <!-- Workflow Bento Grid -->
            <div class="grid grid-cols-2 gap-8">
                <div class="bg-white rounded-[40px] p-8 border border-gallery-outline/10 ambient-shadow hover-lift transition-all">
                    <div class="h-10 w-10 rounded-full bg-gallery-surface-low flex items-center justify-center text-black mb-6">
                        <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14M22 4L12 14.01l-3-3"/></svg>
                    </div>
                    <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-1">Inspection</div>
                    <div class="text-3xl font-bold text-black tracking-tight">Passed</div>
                    <div class="mt-6 flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Verified Today</span>
                    </div>
                </div>
                <div class="bg-white rounded-[40px] p-8 border border-gallery-outline/10 ambient-shadow hover-lift transition-all">
                    <div class="h-10 w-10 rounded-full bg-gallery-surface-low flex items-center justify-center text-black mb-6">
                        <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2.5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    </div>
                    <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-1">Pricing Tier</div>
                    <div class="text-3xl font-bold text-black tracking-tight">Premium</div>
                    <div class="mt-6 flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-zinc-200"></div>
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Fixed Portfolio</span>
                    </div>
                </div>
            </div>

            <!-- History List -->
            <div class="bg-white rounded-[40px] p-10 ambient-shadow border border-gallery-outline/10">
                <h3 class="text-[12px] font-bold text-black uppercase tracking-[0.4em] mb-10 pb-6 border-b border-gallery-outline/10">Status Transition Trail</h3>
                
                <div class="relative space-y-12 before:absolute before:left-[15px] before:top-2 before:bottom-2 before:w-px before:bg-gallery-outline/10">
                    @forelse($unit->statusLogs()->take(5)->get() as $log)
                        <div class="relative flex gap-8 group">
                            <div class="w-8 h-8 rounded-full {{ $log->action === 'SET_AVAILABLE' ? 'bg-emerald-50 text-emerald-600' : 'bg-black text-white' }} flex items-center justify-center flex-shrink-0 z-10 ring-8 ring-white shadow-sm transition-transform group-hover:scale-110">
                                @if($log->action === 'SET_AVAILABLE')
                                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="3"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                @endif
                            </div>
                            <div class="flex-grow">
                                <div class="flex justify-between items-start">
                                    <h4 class="text-sm font-bold text-black uppercase tracking-tight">Status: {{ str_replace('SET_', '', $log->action) }}</h4>
                                    <span class="text-[10px] font-bold text-zinc-300 uppercase tracking-widest">{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-[12px] font-medium text-zinc-400 mt-1 leading-snug">Logged via Mobile Terminal by <span class="text-black">{{ $log->user?->name ?? 'Curator' }}</span></p>
                                @if($log->reason)
                                    <div class="mt-4 p-4 rounded-xl bg-gallery-surface-low border border-gallery-outline/5 text-[11px] font-medium text-zinc-500 italic">
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

                <a href="{{ route('admin.logs.index') }}" class="w-full h-14 mt-12 border border-gallery-outline/30 rounded-2xl flex items-center justify-center text-[10px] font-bold text-zinc-400 hover:text-black hover:border-black transition-all uppercase tracking-[0.2em]">
                    Access Full Audit Log
                </a>
            </div>
        </div>
    </main>

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
                justify-content: center !important;
                align-items: center !important;
                page-break-inside: avoid !important;
                margin: 0 !important;
                padding: 20px !important;
            }
            #print-qr-code svg {
                width: 80vmin !important;
                height: 80vmin !important;
                max-width: 400px !important;
                max-height: 400px !important;
            }
        }
    </style>
</div>
