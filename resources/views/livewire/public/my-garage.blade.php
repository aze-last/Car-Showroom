@php
    use App\Models\Unit;
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="max-w-7xl mx-auto px-container-padding py-stack-lg space-y-16 animate-showroom-fade-up">
    <!-- Header Section -->   
    <div class="flex flex-col md:flex-row justify-between items-end mb-stack-lg gap-gutter">
        <div>
            <h1 class="font-display-lg text-6xl md:text-7xl font-bold tracking-tighter text-primary mb-2">My Garage</h1>  
            <p class="font-body-lg text-lg font-medium text-secondary">Your curated private collection of automotive excellence.</p>
        </div>

        <!-- Floating Summary Panel -->
        <div class="bg-surface-container-lowest border border-outline-variant rounded-3xl p-6 gallery-shadow flex items-center gap-10">     
            <div>
                <span class="font-label-sm text-[10px] font-bold text-secondary block uppercase tracking-[0.2em] mb-1">Portfolio Value</span>
                <span class="font-body-lg text-3xl font-bold text-primary tracking-tight">₱{{ number_format($collectionValue) }}</span>
            </div>
            <div class="h-10 w-px bg-outline-variant/30"></div>    
            <div>
                <span class="font-label-sm text-[10px] font-bold text-secondary block uppercase tracking-[0.2em] mb-1">Acquired Assets</span>
                <span class="font-body-lg text-3xl font-bold text-primary tracking-tight">{{ $acquiredUnits->count() }}</span>   
            </div>
            <a href="{{ route('home') }}" wire:navigate class="bg-primary hover:bg-inverse-surface text-on-primary px-6 py-3 rounded-2xl gallery-transition flex items-center gap-2 ambient-shadow">
                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2.5"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <span class="font-label-bold text-[11px] font-bold uppercase tracking-widest">Discover More</span>
            </a>
        </div>
    </div>

    <!-- Acquired Assets Section -->
    @if($acquiredUnits->isNotEmpty())
    <section class="space-y-12">
        <div class="flex items-center justify-between border-b border-outline-variant/20 pb-8">
            <h3 class="text-[11px] font-bold uppercase tracking-[0.4em] text-emerald-600">Private Collection — Acquired</h3>
            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest italic">Authenticity Verified</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
            @foreach($acquiredUnits as $unit)
                <article class="bg-white border border-emerald-100 rounded-[40px] overflow-hidden gallery-card gallery-transition relative group flex flex-col shadow-xl">
                    <div class="relative h-72 overflow-hidden bg-zinc-100">
                        @if($unit->mainImage)
                            <img src="{{ Storage::url($unit->mainImage->url) }}" alt="{{ $unit->name }}" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105">
                        @endif
                        
                        <!-- Ownership Badge -->
                        <div class="absolute top-6 left-6 bg-emerald-600 text-white px-4 py-2 rounded-full flex items-center gap-2 shadow-lg">      
                            <svg viewBox="0 0 24 24" fill="none" class="h-3 w-3" stroke="currentColor" stroke-width="4"><path d="M20 6L9 17L4 12" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span class="text-[9px] font-bold uppercase tracking-widest">BOUGHT</span>        
                        </div>
                    </div>

                    <div class="p-10 flex flex-col flex-grow space-y-8">
                        <div>
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.2em] mb-2 block">{{ $unit->category?->name ?? 'Asset' }}</span>
                            <h3 class="text-3xl font-bold text-black tracking-tight">{{ $unit->name }}</h3>
                            <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mt-2">Added to Vault: {{ $unit->updated_at->format('M d, Y') }}</p>
                        </div>

                        <div class="mt-auto pt-8 border-t border-zinc-50 flex gap-4">
                            <a href="{{ route('units.show', $unit) }}" wire:navigate class="flex-1 bg-zinc-100 text-black font-bold text-[10px] uppercase tracking-widest py-4 rounded-2xl hover:bg-zinc-200 transition-all text-center">
                                View Registry     
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Main Collection Grid -->
    <section class="space-y-12">
        <div class="flex items-center justify-between border-b border-outline-variant/20 pb-8">
            <h3 class="text-[11px] font-bold uppercase tracking-[0.4em] text-primary">Active Bidding Activity</h3>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            @forelse($myBids as $bid)
                <article class="bg-white border border-outline-variant/30 rounded-[32px] p-8 flex gap-8 ambient-shadow hover-lift group">
                    <div class="w-32 h-24 rounded-2xl overflow-hidden bg-zinc-100 shrink-0">
                        @if($bid->auction->unit->mainImage)
                            <img src="{{ Storage::url($bid->auction->unit->mainImage->url) }}" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="flex-grow space-y-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest mb-1">Lot #{{ $bid->auction->lot_number }}</p>
                                <h4 class="text-xl font-bold text-black">{{ $bid->auction->unit->name }}</h4>
                            </div>
                            <div class="text-right">
                                <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest mb-1">Your Bid</p>
                                <p class="text-lg font-bold text-black">₱{{ number_format($bid->amount_php) }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between pt-4 border-t border-zinc-50">
                            <div class="flex items-center gap-3">
                                @if($bid->auction->current_bid_php === $bid->amount_php)
                                    <span class="flex h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">Leading Bidder</span>
                                @else
                                    <span class="flex h-2 w-2 rounded-full bg-red-500"></span>
                                    <span class="text-[10px] font-bold text-red-600 uppercase tracking-widest">Outbid</span>
                                @endif
                            </div>
                            <a href="{{ route('auction.room', $bid->auction->id) }}" class="text-[10px] font-bold text-black border-b border-black pb-0.5 hover:opacity-50 transition-opacity uppercase tracking-widest">Enter Room</a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full py-20 text-center bg-zinc-50/50 rounded-[32px] border border-dashed border-zinc-200">
                    <p class="text-[10px] font-bold uppercase tracking-[0.4em] text-zinc-400">No active bidding participation</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Main Collection Grid -->
    <section class="space-y-12">
        <div class="flex items-center justify-between border-b border-outline-variant/20 pb-8">
            <h3 class="text-[11px] font-bold uppercase tracking-[0.4em] text-primary">Curated Portfolio</h3>
            <a href="{{ route('home') }}" class="text-[10px] font-bold text-primary border-b-2 border-primary pb-1 hover:opacity-60 transition-all uppercase tracking-widest">Discover More</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
            @forelse($savedUnits as $unit)
                <article class="bg-surface-container-lowest border border-outline-variant rounded-[40px] overflow-hidden gallery-card gallery-transition relative group flex flex-col ambient-shadow hover-lift">
                    <div class="relative h-72 overflow-hidden bg-gallery-surface-low">
                        @if($unit->mainImage)
                            <img src="{{ Storage::url($unit->mainImage->url) }}" alt="{{ $unit->name }}" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105 {{ $unit->status === Unit::STATUS_SOLD ? 'grayscale opacity-60' : '' }}">
                        @endif
                        
                        <!-- Status Badge -->
                        <div class="absolute top-6 left-6 bg-white/90 backdrop-blur-md px-4 py-1.5 rounded-full border border-outline-variant/30 flex items-center gap-2 shadow-sm">      
                            <div class="w-2 h-2 rounded-full {{ $unit->status === Unit::STATUS_AVAILABLE ? 'bg-emerald-500 animate-pulse' : 'bg-zinc-400' }}"></div>
                            <span class="font-label-sm text-[9px] font-bold text-primary uppercase tracking-widest">{{ $unit->status }}</span>        
                        </div>

                        <!-- Remove Action -->
                        <button wire:click="removeUnit({{ $unit->id }})" class="absolute top-6 right-6 text-zinc-400 hover:text-red-600 bg-white/90 backdrop-blur-md p-2.5 rounded-full gallery-transition opacity-0 group-hover:opacity-100 shadow-lg border border-red-50">   
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18m-2 0v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6m3 0V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                        </button>
                    </div>

                    <div class="p-10 flex flex-col flex-grow space-y-8">
                        <div class="flex justify-between items-start"> 
                            <div>
                                <span class="font-label-sm text-[10px] font-bold text-secondary uppercase tracking-[0.2em] mb-2 block">{{ $unit->category?->name ?? 'Asset' }}</span>
                                <h3 class="font-display-lg text-3xl font-bold text-primary tracking-tight group-hover:text-zinc-500 transition-colors">{{ $unit->name }}</h3>
                            </div>
                            <span class="font-body-lg text-xl font-bold text-primary tracking-tight">{{ $unit->formattedPrice() }}</span>
                        </div>

                        <div class="mt-auto pt-8 border-t border-outline-variant/10 flex gap-4">
                            <a href="{{ route('units.show', $unit) }}" wire:navigate class="flex-1 bg-black text-white font-bold text-[10px] uppercase tracking-widest py-4 rounded-2xl hover:bg-zinc-800 transition-all text-center ambient-shadow">
                                Open Exhibit     
                            </a>
                            <button wire:click="$toggle('compareIds')" class="p-4 border border-outline-variant/40 rounded-2xl text-zinc-400 hover:text-black hover:border-black gallery-transition group/btn">
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 group-hover/btn:scale-110 transition-transform" stroke="currentColor" stroke-width="2.5"><path d="M16 3L21 8L16 13M8 21L3 16L8 11"/></svg>
                            </button>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full py-40 text-center bg-white rounded-[48px] border border-dashed border-outline-variant/30 animate-showroom-fade-up">
                    <div class="mb-8 opacity-10">
                        <svg viewBox="0 0 24 24" fill="none" class="h-24 w-24 mx-auto text-black" stroke="currentColor" stroke-width="1"><path d="M4 8.5L12 4L20 8.5V15.5L12 20L4 15.5V8.5Z" stroke-linejoin="round"/></svg>
                    </div>
                    <p class="text-[12px] font-bold uppercase tracking-[0.6em] text-zinc-300">Your digital collection is empty</p>
                    <a href="{{ route('home') }}" class="mt-12 inline-block px-12 py-5 bg-black text-white rounded-full font-bold text-[11px] uppercase tracking-[0.3em] shadow-2xl hover:scale-105 transition-all">Curate My Gallery</a>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Collector ID Card -->
    <footer class="pt-20 border-t border-outline-variant/20 flex flex-col md:flex-row justify-between items-center gap-12">
        <div class="flex items-center gap-8">
            <div class="h-20 w-20 rounded-full bg-primary text-on-primary flex items-center justify-center font-bold text-2xl shadow-2xl">
                {{ auth()->user()->initials() }}
            </div>
            <div>
                <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.4em] mb-2">Verified Collector</p>
                <h4 class="text-3xl font-bold text-primary tracking-tighter">{{ auth()->user()->name }}</h4>
                <p class="text-sm font-medium text-secondary mt-1">Portfolio Curator since {{ auth()->user()->created_at->format('M Y') }}</p>
            </div>
        </div>
        
        <div class="flex gap-6">
             <form method="POST" action="{{ route('logout') }}" x-data>
                @csrf
                <button type="submit" class="px-10 py-4 border border-outline-variant rounded-full font-bold text-[10px] uppercase tracking-[0.3em] text-secondary hover:text-primary hover:border-primary transition-all">Terminal Exit</button>
            </form>
            <button class="px-10 py-4 bg-surface-container-high rounded-full font-bold text-[10px] uppercase tracking-[0.3em] text-primary hover:bg-surface-variant transition-all">Vault Settings</button>
        </div>
    </footer>
</div>
