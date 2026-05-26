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
                <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mb-8 shadow-inner">
                    <svg viewBox="0 0 24 24" fill="none" class="h-10 w-10 text-emerald-600" stroke="currentColor" stroke-width="3"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14M22 4L12 14.01l-3-3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h2 class="text-3xl font-bold tracking-tighter text-black mb-2">Scan Successful</h2>
                <p class="text-sm font-medium text-zinc-400 mb-10">Vehicle authenticated and locked for your current curator session.</p>
                
                <div class="w-full bg-gallery-surface-low rounded-3xl p-6 flex items-center gap-6 text-left border border-gallery-outline/5 group-hover:bg-white transition-colors duration-500">
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
                <div class="space-y-4 pt-6 border-t border-gallery-outline/5">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-widest px-1">Assign Buyer</label>
                        <select wire:model="buyer_id" class="w-full bg-white border border-gallery-outline/20 rounded-2xl py-4 px-6 font-bold text-sm focus:ring-2 focus:ring-black transition-all appearance-none">
                            <option value="">Select Collector...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
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
</div>
