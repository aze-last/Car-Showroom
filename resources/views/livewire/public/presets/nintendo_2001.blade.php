@use('App\Models\Unit')
@use('Illuminate\Support\Facades\Storage')

<div class="nintendo-preset min-h-screen bg-[#7a8aba] text-[#21242e] font-sans pb-24" 
     x-data="{ scrollY: 0 }" 
     @scroll.window="scrollY = window.scrollY">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=VT323&display=swap');

        .nintendo-preset {
            --n-red: #e60012;
            --n-orange: #f68d1f;
            --n-amber: #ecab37;
            --n-gold: #e48600;
            --n-canvas: #7a8aba;
            --n-canvas-soft: #9fbee7;
            --n-carbon: #21242e;
            --n-indigo: #3d4f97;
            --n-platinum: #dedede;
            --n-surface: #ffffff;
        }

        .beveled-plate {
            background: var(--n-canvas);
            border-top: 2px solid #ffffff;
            border-left: 2px solid #ffffff;
            border-right: 2px solid var(--n-indigo);
            border-bottom: 2px solid var(--n-indigo);
            box-shadow: inset 1px 1px 0 rgba(255,255,255,0.5), 2px 2px 5px rgba(0,0,0,0.2);
        }

        .beveled-plate-inset {
            background: var(--n-platinum);
            border-top: 2px solid var(--n-indigo);
            border-left: 2px solid var(--n-indigo);
            border-right: 2px solid #ffffff;
            border-bottom: 2px solid #ffffff;
        }

        .nintendo-display {
            font-family: 'Arial Black', Gadget, sans-serif;
            text-transform: uppercase;
            color: white;
            text-shadow: 
                -2px -2px 0 #000,  
                 2px -2px 0 #000,
                -2px  2px 0 #000,
                 2px  2px 0 #000,
                 4px  4px 0 rgba(0,0,0,0.5);
        }

        .nintendo-label {
            font-family: Arial, sans-serif;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 11px;
        }

        .carbon-slab {
            background-color: var(--n-carbon);
            background-image: radial-gradient(rgba(255,255,255,0.05) 1px, transparent 0);
            background-size: 4px 4px;
        }

        .chamfered {
            clip-path: polygon(
                10px 0%, calc(100% - 10px) 0%, 
                100% 10px, 100% calc(100% - 10px), 
                calc(100% - 10px) 100%, 10px 100%, 
                0% calc(100% - 10px), 0% 10px
            );
        }

        .nintendo-button-orange {
            background: var(--n-orange);
            border-top: 2px solid #ffb066;
            border-left: 2px solid #ffb066;
            border-right: 2px solid #a65a00;
            border-bottom: 2px solid #a65a00;
            color: white;
            font-weight: 900;
            text-transform: uppercase;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .nintendo-button-orange:active {
            border: 2px solid #a65a00;
            border-top-color: #a65a00;
            border-left-color: #a65a00;
            border-right-color: #ffb066;
            border-bottom-color: #ffb066;
            transform: translate(1px, 1px);
        }
        
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    <!-- 1. Retro Masthead -->
    <header class="w-full max-w-5xl mx-auto px-4 pt-8 pb-4 flex justify-between items-end">
        <div class="flex items-center gap-4">
            <div class="bg-white rounded-lg p-4 beveled-plate relative">
                <p class="text-[10px] font-bold uppercase text-[#21242e] leading-tight">Welcome to<br>The Gallery</p>
                <div class="absolute -bottom-2 right-4 w-4 h-4 bg-white rotate-45 border-r-2 border-b-2 border-[#3d4f97]"></div>
            </div>
        </div>
        
        <div class="flex items-center gap-2">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="SEARCH CATALOG" class="beveled-plate-inset px-4 h-8 text-[11px] font-bold uppercase w-48 focus:outline-none bg-white">
            <button class="nintendo-button-orange px-4 h-8">GO</button>
        </div>
    </header>

    <!-- 2. Dual Nav Bar -->
    <div class="w-full max-w-5xl mx-auto mb-8 px-4">
        <nav class="carbon-slab h-10 flex items-center px-6 gap-8 border-b border-[#5a5f8c]">
            <div class="bg-white rounded-full px-3 py-0.5 border-2 border-[#e60012]">
                <span class="text-[#e60012] font-black italic text-lg tracking-tighter">NINTENDO</span>
            </div>
            <div class="flex gap-6 overflow-x-auto no-scrollbar">
                @foreach($categories as $category)
                    <button wire:click="$set('categoryId', {{ $category->id }})" class="nintendo-label shrink-0 {{ $categoryId === $category->id ? 'text-white underline' : 'text-[#e48600] hover:text-white' }}">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>
        </nav>
        <div class="bg-[#9fbee7] h-6 flex items-center px-6 gap-6 text-[10px] font-bold text-[#3d4f97] uppercase overflow-x-auto no-scrollbar">
            <span>Customer Service</span>
            <span>Corporate</span>
            <span>Contact</span>
            <span class="ml-auto">Parent's Guide</span>
        </div>
    </div>

    <!-- 3. Hero Section (Circuited Backdrop) -->
    @if($featuredUnits->isNotEmpty())
        @php $hero = $featuredUnits->first(); @endphp
        <section class="w-full max-w-5xl mx-auto mb-12 px-4">
            <div class="bg-[#acace7] beveled-plate p-1 relative overflow-hidden chamfered">
                <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#3d4f97 1px, transparent 0); background-size: 20px 20px;"></div>
                <div class="relative flex flex-col md:flex-row min-h-[400px]">
                    <div class="md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
                        <span class="bg-[#f68d1f] text-white nintendo-label px-3 py-1 w-fit mb-4 chamfered shadow-md">PLAY IT NOW</span>
                        <h2 class="nintendo-display text-4xl md:text-5xl mb-6 leading-tight">{{ $hero->name }}</h2>
                        <p class="font-sans text-sm font-bold text-[#21242e] mb-8 leading-relaxed">
                            {{ $hero->description ?? 'Experience the power of the next generation of performance.' }}
                        </p>
                        <a href="{{ route('units.show', $hero) }}" wire:navigate class="nintendo-button-orange w-fit px-10 h-12 text-sm shadow-xl">
                            EXPLORE SYSTEM <span class="text-xl">&rarr;</span>
                        </a>
                    </div>
                    <div class="md:w-1/2 relative min-h-[300px]">
                        @if($hero->mainImage)
                            <img src="{{ Storage::url($hero->mainImage->url) }}" class="w-full h-full object-cover grayscale-[0.2] contrast-125" alt="{{ $hero->name }}">
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-r from-[#acace7] via-transparent to-transparent hidden md:block"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-[#acace7] via-transparent to-transparent md:hidden"></div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- 4. Main Content (Two Columns) -->
    <main class="w-full max-w-5xl mx-auto flex flex-col md:flex-row gap-8 px-4">
        <!-- Content Column (2/3) -->
        <div class="md:w-2/3 space-y-8">
            <!-- News Bar Header -->
            <div class="space-y-4">
                <div class="bg-[#7a8aba] beveled-plate px-4 py-2 flex items-center gap-2">
                    <div class="grid grid-cols-2 gap-0.5">
                        <div class="w-1 h-1 bg-black"></div><div class="w-1 h-1 bg-black"></div>
                        <div class="w-1 h-1 bg-black"></div><div class="w-1 h-1 bg-black"></div>
                    </div>
                    <h3 class="nintendo-label text-[#21242e]">Official Gallery Lineup</h3>
                </div>

                <div class="space-y-2">
                    @forelse ($units as $unit)
                        <article class="bg-[#dedede] beveled-plate hover:bg-white transition-colors group relative overflow-hidden flex flex-col sm:flex-row items-center p-3 gap-6">
                            <a href="{{ route('units.show', $unit) }}" wire:navigate class="absolute inset-0 z-10"></a>
                            <div class="w-full sm:w-32 aspect-[4/3] bg-black beveled-plate-inset overflow-hidden shrink-0">
                                @if($unit->mainImage)
                                    <img src="{{ Storage::url($unit->mainImage->url) }}" class="w-full h-full object-cover grayscale-[0.3] group-hover:grayscale-0 transition-all" alt="{{ $unit->name }}">
                                @endif
                            </div>
                            <div class="flex-1 min-w-0 w-full text-center sm:text-left">
                                <div class="flex justify-between items-start mb-1">
                                    <h4 class="font-bold text-[#3d4f97] text-sm group-hover:underline truncate w-full sm:w-auto">{{ $unit->name }}</h4>
                                    <span class="bg-[#f68d1f] text-white p-1 chamfered shrink-0 hidden sm:block">
                                        <svg viewBox="0 0 24 24" fill="none" class="h-3 w-3" stroke="currentColor" stroke-width="4"><path d="M9 18L15 12L9 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </span>
                                </div>
                                <p class="text-[11px] text-[#60619c] font-bold uppercase tracking-tight mb-2">
                                    {{ $unit->year }} // {{ $unit->category?->name }} // {{ $unit->transmission }}
                                </p>
                                <p class="font-bold text-[#21242e] text-sm sm:hidden">{{ $unit->formattedPrice() }}</p>
                            </div>
                            <div class="text-right shrink-0 hidden sm:block">
                                <p class="font-bold text-[#21242e] text-sm">{{ $unit->formattedPrice() }}</p>
                            </div>
                        </article>
                    @empty
                        <div class="bg-[#dedede] beveled-plate p-12 text-center">
                            <p class="nintendo-label text-[#60619c]">No units found in database.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            
            <div class="pt-4">
                {{ $units->links() }}
            </div>
        </div>

        <!-- Action Rail (1/3) -->
        <aside class="md:w-1/3 space-y-8">
            <div class="carbon-slab p-6 beveled-plate space-y-4 chamfered">
                <button class="w-full nintendo-button-orange h-10 chamfered">LOGIN TO GARAGE</button>
                <button class="w-full bg-[#21242e] border-2 border-[#5a5f8c] text-white nintendo-label h-10 hover:bg-[#3d4f97] transition-colors">SUBSCRIBE</button>
                <button class="w-full bg-[#21242e] border-2 border-[#5a5f8c] text-white nintendo-label h-10 hover:bg-[#3d4f97] transition-colors">NEWSLETTER</button>
            </div>

            <div class="bg-white beveled-plate p-6 space-y-4">
                <div class="bg-[#ecab37] nintendo-label text-[#21242e] px-4 py-1 w-fit -mt-8 -ml-8 beveled-plate">
                    What Is This?
                </div>
                <p class="text-xs font-sans text-[#21242e] leading-relaxed">
                    Welcome to the 2001 Gallery Catalog. Explore our collection of premium vehicles through our hardware-inspired interface.
                </p>
            </div>

            <div class="bg-[#acace7] beveled-plate p-6 relative overflow-hidden chamfered">
                <div class="relative z-10">
                    <h4 class="nintendo-display text-lg mb-4">SYSTEM POWER</h4>
                    <p class="text-[10px] font-bold text-[#3d4f97] mb-4 uppercase">Now in 32-Bit Aesthetics</p>
                    <button class="nintendo-button-orange px-4 py-1 text-[10px] chamfered">LEARN MORE</button>
                </div>
            </div>
        </aside>
    </main>

    <!-- 5. Footer -->
    <footer class="mt-24 border-t-4 border-[#3d4f97] px-4">
        <div class="carbon-slab py-12 px-8 chamfered max-w-5xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="flex flex-col md:flex-row items-center gap-6 text-center md:text-left">
                    <div class="bg-[#ecab37] beveled-plate px-4 py-2 text-[10px] font-bold text-[#21242e]">
                        ESRB - PRIVACY CERTIFIED
                    </div>
                    <p class="text-[10px] text-[#9fbee7] font-bold">© 1997-2001 NINTENDO. ALL RIGHTS RESERVED.</p>
                </div>
                <div class="flex gap-4">
                    <div class="w-8 h-8 bg-white flex items-center justify-center font-black text-black text-xl beveled-plate">E</div>
                    <div class="w-8 h-8 bg-white flex items-center justify-center font-black text-black text-xl beveled-plate">T</div>
                </div>
            </div>
        </div>
    </footer>
</div>
