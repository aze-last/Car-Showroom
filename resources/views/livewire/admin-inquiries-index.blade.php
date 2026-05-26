@php
    use App\Models\Inquiry;
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="h-[calc(100vh-160px)] -m-8 flex overflow-hidden animate-showroom-fade-up">
    <!-- Left Pane: Lead Pipeline -->
    <aside class="w-full max-w-[400px] border-r border-gallery-outline/10 bg-gallery-surface-low/30 flex flex-col overflow-hidden">
        <!-- List Header -->
        <div class="p-8 border-b border-gallery-outline/10 bg-white/50 backdrop-blur-md flex justify-between items-center shrink-0">
            <div>
                <h2 class="text-[11px] font-bold text-black uppercase tracking-[0.4em]">Lead Pipeline</h2>
                <p class="text-[10px] font-medium text-zinc-400 mt-1">{{ $inquiries->total() }} Active Inquiries</p>
            </div>
        </div>

        <!-- Search Leads -->
        <div class="p-4 border-b border-gallery-outline/10">
            <div class="relative">
                <svg viewBox="0 0 24 24" fill="none" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-zinc-400" stroke="currentColor" stroke-width="3"><circle cx="11" cy="11" r="7"/><path d="M20 20L16.65 16.65" stroke-linecap="round"/></svg>
                <input 
                    wire:model.live.debounce.300ms="search"
                    class="w-full bg-white border border-gallery-outline/10 text-xs font-medium rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-black/5 transition-all shadow-sm" 
                    placeholder="Search name, email, vehicle..." 
                    type="text"
                />
            </div>
        </div>

        <!-- List Content -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3">
            @forelse ($inquiries as $inquiry)
                <div 
                    wire:click="selectInquiry({{ $inquiry->id }})"
                    wire:key="inquiry-list-{{ $inquiry->id }}"
                    class="p-5 rounded-2xl bg-white border cursor-pointer transition-all duration-300 relative overflow-hidden group hover-lift {{ $selectedInquiryId === $inquiry->id ? 'border-black ambient-shadow ring-1 ring-black' : 'border-gallery-outline/10 opacity-70 hover:opacity-100' }}"
                >
                    @if($selectedInquiryId === $inquiry->id)
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-black"></div>
                    @endif

                    <div class="flex justify-between items-start mb-3"> 
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gallery-surface-low border border-gallery-outline/10 flex items-center justify-center text-black font-bold text-xs">
                                {{ substr($inquiry->name, 0, 2) }}
                            </div>
                            <div>
                                <h4 class="text-[13px] font-bold text-black tracking-tight">{{ $inquiry->name }}</h4>
                                <span class="text-[10px] font-medium text-zinc-400">{{ $inquiry->created_at->diffForHumans() }}</span>      
                            </div>
                        </div>
                        <span class="px-2 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest {{ $inquiry->status === 'new' || $inquiry->status === 'unread' ? 'bg-black text-white' : 'bg-gallery-surface-low text-zinc-500' }}">
                            {{ $inquiry->status }}
                        </span>
                    </div>

                    <div class="pl-1 text-zinc-500 text-[11px] font-medium line-clamp-1">
                        Interested in: {{ $inquiry->unit?->name ?? 'Asset' }}
                    </div>
                </div>
            @empty
                <div class="py-20 text-center opacity-30">
                    <span class="text-[10px] font-bold uppercase tracking-[0.4em]">Empty Pipeline</span>
                </div>
            @endforelse
        </div>

        <div class="p-4 border-t border-gallery-outline/10 bg-white/30 backdrop-blur-md shrink-0">
            {{ $inquiries->links() }}
        </div>
    </aside>

    <!-- Right Pane: Inquiry Detailed Inspection -->
    <main class="flex-1 bg-white overflow-y-auto relative">
        @if ($selectedInquiry)
            <div class="max-w-4xl mx-auto p-12 space-y-16 pb-32">    
                <!-- Detail Header -->    
                <div class="flex flex-col md:flex-row items-start md:items-end justify-between border-b border-gallery-outline/10 pb-10 gap-6">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <span class="px-3 py-1 rounded-full bg-black text-white font-bold text-[9px] uppercase tracking-widest">Active Lead</span>
                            <span class="text-zinc-300 font-bold text-[10px] uppercase tracking-widest">ID: INQ-{{ str_pad($selectedInquiry->id, 4, '0', STR_PAD_LEFT) }}</span>  
                        </div>
                        <h2 class="text-5xl font-bold text-black tracking-tighter leading-none mb-6">{{ $selectedInquiry->name }}</h2>
                        <div class="flex flex-wrap items-center gap-6 text-zinc-500">
                            <div class="flex items-center gap-2">
                                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                <span class="text-sm font-medium">{{ $selectedInquiry->email }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                <span class="text-sm font-medium">{{ $selectedInquiry->phone ?? 'Not Provided' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-3">  
                        <button wire:click="delete({{ $selectedInquiry->id }})" wire:confirm="Archive this lead?" class="px-6 py-3 rounded-2xl border border-gallery-outline/30 text-zinc-300 hover:text-red-600 hover:border-red-100 font-bold text-[10px] uppercase tracking-widest transition-all"> 
                            Archive
                        </button>
                    </div>
                </div>

                <!-- Split Content: Asset vs Timeline -->
                <div class="grid grid-cols-12 gap-12">
                    <!-- Linked Unit Card -->
                    <div class="col-span-12 xl:col-span-7 space-y-6">
                        <h3 class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.4em]">Asset of Interest</h3>
                        @if ($selectedInquiry->unit)
                            <a href="{{ route('units.show', $selectedInquiry->unit) }}" target="_blank" class="block rounded-[32px] border border-gallery-outline/10 bg-white overflow-hidden ambient-shadow hover-lift transition-all group">
                                <div class="h-64 bg-gallery-surface-low relative overflow-hidden">
                                    @if($selectedInquiry->unit->mainImage)
                                        <img src="{{ Storage::url($selectedInquiry->unit->mainImage->url) }}" alt="" class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105">
                                    @endif
                                    <div class="absolute top-6 right-6 bg-black text-white px-4 py-2 rounded-full font-bold text-xs shadow-xl">    
                                        {{ $selectedInquiry->unit->formattedPrice() }}
                                    </div>
                                </div>
                                <div class="p-8">
                                    <div class="flex justify-between items-start"> 
                                        <div>
                                            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-2">{{ $selectedInquiry->unit->year ?? 'Catalog' }} • {{ $selectedInquiry->unit->category?->name ?? 'Premium' }}</p>
                                            <h4 class="text-3xl font-bold text-black tracking-tight group-hover:text-zinc-500 transition-colors">{{ $selectedInquiry->unit->name }}</h4>
                                        </div>
                                        <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6 text-zinc-300 group-hover:text-black transition-colors" stroke="currentColor" stroke-width="2.5"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                                    </div>
                                </div>
                            </a>
                        @else
                            <div class="p-10 rounded-[32px] border border-gallery-outline/10 border-dashed text-center opacity-30">
                                <span class="text-[10px] font-bold uppercase tracking-widest">Asset Unlinked or Deleted</span>
                            </div>
                        @endif
                    </div>

                    <!-- Timeline -->
                    <div class="col-span-12 xl:col-span-5 space-y-6">
                        <h3 class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.4em]">Engagement History</h3>
                        <div class="relative space-y-8 before:absolute before:left-5 before:top-2 before:bottom-2 before:w-px before:bg-gallery-outline/10">
                            <!-- Initial Inquiry -->
                            <div class="relative flex items-start gap-8">       
                                <div class="absolute left-0 w-10 h-10 flex items-center justify-center">      
                                    <div class="w-2.5 h-2.5 rounded-full bg-black z-10 ring-8 ring-white"></div>
                                </div>
                                <div class="ml-10 w-full">
                                    <div class="flex justify-between items-start mb-2"> 
                                        <h5 class="text-[12px] font-bold text-black uppercase tracking-tight">Lead Capture</h5>
                                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">{{ $selectedInquiry->created_at->format('M d') }}</span>      
                                    </div>
                                    <div class="p-6 rounded-2xl bg-gallery-surface-low border border-gallery-outline/10 text-sm font-medium text-zinc-600 leading-relaxed italic">
                                        "{{ $selectedInquiry->message }}"
                                    </div>
                                </div>
                            </div>

                            <!-- Status Timeline Item -->
                            <div class="relative flex items-start gap-8">       
                                <div class="absolute left-0 w-10 h-10 flex items-center justify-center">      
                                    <div class="w-2.5 h-2.5 rounded-full bg-zinc-200 z-10 ring-8 ring-white"></div>
                                </div>
                                <div class="ml-10 pt-1 w-full">
                                    <div class="flex justify-between items-start mb-1"> 
                                        <h5 class="text-[12px] font-bold text-zinc-400 uppercase tracking-tight">Status Update</h5>
                                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">Logged</span>      
                                    </div>
                                    <p class="text-[11px] font-medium text-zinc-400">Current Lifecycle: <strong class="text-black uppercase">{{ $selectedInquiry->status }}</strong></p>    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Floating Action Console -->
                @if($selectedInquiry->status !== 'closed')
                    <div class="fixed bottom-8 right-12 bg-black text-white rounded-full shadow-2xl px-8 py-5 flex items-center gap-10 z-50 animate-showroom-fade-up">
                        <div class="flex flex-col">
                            <span class="text-[9px] font-bold text-zinc-500 uppercase tracking-[0.2em]">Next Step Required</span>
                            <span class="text-[11px] font-bold uppercase tracking-widest">Progress Lifecycle</span>
                        </div>
                        <div class="h-8 w-px bg-zinc-800"></div>  
                        
                        <div class="flex gap-6">
                            <button wire:click="setStatus({{ $selectedInquiry->id }}, 'contacted')" class="flex items-center gap-2 font-bold text-[10px] uppercase tracking-widest {{ $selectedInquiry->status === 'contacted' ? 'text-emerald-500' : 'text-zinc-400 hover:text-white' }} transition-colors">
                                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                Contacted
                            </button>
                            <button wire:click="setStatus({{ $selectedInquiry->id }}, 'negotiating')" class="flex items-center gap-2 font-bold text-[10px] uppercase tracking-widest {{ $selectedInquiry->status === 'negotiating' ? 'text-emerald-500' : 'text-zinc-400 hover:text-white' }} transition-colors">
                                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                Negotiating
                            </button>
                            <button wire:click="setStatus({{ $selectedInquiry->id }}, 'closed')" class="flex items-center gap-2 font-bold text-[10px] uppercase tracking-widest {{ $selectedInquiry->status === 'closed' ? 'text-emerald-500' : 'text-zinc-400 hover:text-white' }} transition-colors">
                                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="3"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                Close
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="h-full flex items-center justify-center opacity-20">
                <span class="text-[12px] font-bold uppercase tracking-[0.6em]">Select a Lead to Inspect</span>
            </div>
        @endif
    </main>
</div>
