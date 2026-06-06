<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet"/>
        @include('partials.theme-styles')
        <style>
            @keyframes liquid-drift {
                0% { transform: scale(1) translate(0, 0); }
                50% { transform: scale(1.1) translate(2%, 1%); }
                100% { transform: scale(1) translate(0, 0); }
            }
            .animate-liquid-drift {
                animation: liquid-drift 20s ease-in-out infinite;
            }
        </style>
    </head>
    @php
        use App\Models\Setting;
        $shopName = Setting::get('shop_name', 'The Gallery');
        $logo = Setting::get('design_logo_path');
    @endphp
    <body class="min-h-screen bg-white antialiased font-hanken text-zinc-900 overflow-hidden">
        <div class="relative grid h-screen flex-col items-center justify-center lg:max-w-none lg:grid-cols-2 lg:px-0">
            <!-- Left Side: Immersive Visual -->
            <div class="relative hidden h-full flex-col bg-black p-12 text-white lg:flex overflow-hidden group">
                <!-- Background Image with Liquid Drift -->
                <div class="absolute inset-0 z-0">
                    <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=2070&auto=format&fit=crop" class="h-full w-full object-cover opacity-60 animate-liquid-drift" alt="Luxury Automobile">
                    <div class="absolute inset-0 bg-gradient-to-tr from-black via-black/40 to-transparent"></div>
                    <div class="absolute inset-0 backdrop-blur-[2px] group-hover:backdrop-blur-none transition-all duration-1000"></div>
                </div>

                <a href="{{ route('home') }}" class="relative z-20 flex items-center gap-4 transition-all hover:translate-x-2" wire:navigate>
                    @if($logo)
                        <img src="{{ Storage::url($logo) }}" class="h-10 w-auto object-contain" alt="{{ $shopName }}">
                    @else
                        <div class="h-12 w-12 rounded-2xl bg-white text-black flex items-center justify-center font-bold text-sm shadow-[0_0_40px_rgba(255,255,255,0.2)]">
                            {{ substr($shopName, 0, 1) }}
                        </div>
                    @endif
                    <span class="text-2xl font-bold tracking-tighter text-white drop-shadow-lg">{{ $shopName }}</span>
                </a>

                @php
                    $quotes = [
                        ['The automobile is the ultimate art form.', 'Enzo Ferrari'],
                        ['Speed has never killed anyone, suddenly becoming stationary… that’s what gets you.', 'Jeremy Clarkson'],
                        ['A dream without ambition is like a car without gas… you’re not going anywhere.', 'Sean Combs'],
                        ['Excellence is not a skill. It is an attitude.', 'Ralph Marston'],
                    ];
                    [$message, $author] = $quotes[array_rand($quotes)];
                @endphp

                <div class="relative z-20 mt-auto max-w-xl animate-showroom-fade-up">
                    <div class="h-1 w-20 bg-brand-primary mb-10 rounded-full"></div>
                    <blockquote class="space-y-6">
                        <p class="text-4xl font-bold tracking-tight leading-[1.1] opacity-95">
                            &ldquo;{{ $message }}&rdquo;
                        </p>
                        <footer class="flex items-center gap-4">
                            <span class="text-[10px] font-black uppercase tracking-[0.5em] text-brand-primary">Curated by</span>
                            <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/40 italic">{{ $author }}</span>
                        </footer>
                    </blockquote>
                </div>
            </div>

            <!-- Right Side: Form Content -->
            <div class="w-full lg:p-12 bg-gallery-background h-full flex flex-col justify-center relative overflow-hidden">
                <!-- Subtle background decoration -->
                <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 w-96 h-96 bg-brand-primary/5 rounded-full blur-[100px]"></div>
                <div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/2 w-64 h-64 bg-zinc-200 rounded-full blur-[80px]"></div>

                <div class="mx-auto flex w-full flex-col justify-center space-y-12 sm:w-[440px] relative z-10">
                    <div class="flex flex-col items-center lg:items-start space-y-4 animate-showroom-fade-up">
                        <a href="{{ route('home') }}" class="z-20 mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-zinc-950 text-xs font-black text-white shadow-2xl lg:hidden transform hover:scale-110 transition-transform" wire:navigate>
                            @if($logo)
                                <img src="{{ Storage::url($logo) }}" class="h-6 w-auto" alt="">
                            @else
                                {{ substr($shopName, 0, 1) }}
                            @endif
                        </a>
                        
                        <div class="flex items-center gap-3">
                            <span class="h-px w-8 bg-brand-primary"></span>
                            <h2 class="text-[10px] font-black uppercase tracking-[0.5em] text-brand-primary">Administrative Gateway</h2>
                        </div>
                        <h1 class="text-4xl font-bold tracking-tighter text-black leading-none">Command Center</h1>
                        <p class="text-[13px] text-zinc-400 font-medium leading-relaxed max-w-sm">
                            Authorized access to the <strong>{{ $shopName }}</strong> inventory and auction protocols.
                        </p>
                    </div>

                    <div class="showroom-auth-content p-8 md:p-10 bg-white/70 backdrop-blur-2xl rounded-[40px] border border-white shadow-[0_32px_80px_-20px_rgba(0,0,0,0.08)] animate-showroom-fade-up" style="animation-delay: 100ms">
                        {{ $slot }}
                    </div>

                    <div class="text-center lg:text-left pt-8 animate-showroom-fade-up" style="animation-delay: 200ms">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-3 text-[10px] font-black uppercase tracking-[0.2em] text-zinc-300 hover:text-black transition-all group">
                            <div class="h-8 w-8 rounded-full border border-zinc-100 flex items-center justify-center group-hover:border-zinc-900 group-hover:bg-zinc-950 group-hover:text-white transition-all">
                                <svg viewBox="0 0 24 24" fill="none" class="h-3 w-3" stroke="currentColor" stroke-width="3"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                            </div>
                            Exit to Showroom
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
