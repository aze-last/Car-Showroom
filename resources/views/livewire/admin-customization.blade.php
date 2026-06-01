<div class="space-y-12 pb-24">
    <header class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div>
            <h1 class="text-4xl font-bold text-black tracking-tighter">Webapp Customization</h1>
            <p class="text-zinc-500 font-medium mt-2 uppercase text-xs tracking-widest">Orchestrate the digital experience of your showroom</p>
        </div>
        <div class="flex gap-4">
            <button wire:click="save" class="bg-black text-white font-black uppercase tracking-widest text-[11px] px-10 py-4 rounded-2xl hover:scale-105 transition-all shadow-xl shadow-black/10">
                Synchronize Changes
            </button>
        </div>
    </header>

    @if (session('status'))
        <div class="p-6 bg-emerald-50 border border-emerald-100 rounded-[30px] animate-showroom-fade-up">
            <p class="text-xs font-bold text-emerald-600 uppercase tracking-widest text-center">
                {{ session('status') }}
            </p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Left Column: Visual Identity -->
        <div class="lg:col-span-2 space-y-12">
            <!-- 1. Color Palette -->
            <section class="bg-white rounded-[40px] border border-zinc-100 p-10 space-y-8 shadow-sm">
                <div>
                    <h3 class="text-lg font-bold text-black tracking-tight">Visual Identity</h3>
                    <p class="text-xs text-zinc-400 font-medium uppercase tracking-widest mt-1">Accent Palettes & Brand Tone</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($palettes as $key => $p)
                        <button 
                            wire:click="$set('palette', '{{ $key }}')"
                            class="flex items-center gap-4 p-6 rounded-[25px] border-2 transition-all group {{ $palette === $key ? 'border-black bg-zinc-50' : 'border-zinc-50 hover:border-zinc-200 bg-white' }}"
                        >
                            <div class="h-12 w-12 rounded-2xl flex items-center justify-center shadow-lg transition-transform group-hover:scale-110" style="background-color: {{ $p['primary'] }}">
                                <div class="h-4 w-4 rounded-full bg-white/20 animate-pulse"></div>
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-bold text-black">{{ $p['name'] }}</p>
                                <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest">Base: {{ $p['primary'] }}</p>
                            </div>
                            @if($palette === $key)
                                <div class="ml-auto">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-black" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                </div>
                            @endif
                        </button>
                    @endforeach
                </div>
            </section>

            <!-- 2. Hero Content -->
            <section class="bg-white rounded-[40px] border border-zinc-100 p-10 space-y-8 shadow-sm">
                <div>
                    <h3 class="text-lg font-bold text-black tracking-tight">Hero Experience</h3>
                    <p class="text-xs text-zinc-400 font-medium uppercase tracking-widest mt-1">Configure the Cinema Banner</p>
                </div>

                <div class="space-y-6">
                    <flux:field>
                        <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Featured Asset</flux:label>
                        <flux:select wire:model="hero_unit_id" class="rounded-2xl py-4">
                            <option value="">Automated (Most Recent)</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:field>
                            <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Headline</flux:label>
                            <flux:input wire:model="hero_headline" placeholder="e.g. Automotive Excellence" class="rounded-2xl py-4" />
                        </flux:field>

                        <flux:field>
                            <flux:label class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Subtitle</flux:label>
                            <flux:input wire:model="hero_subtitle" placeholder="e.g. Curated collection for the discerning..." class="rounded-2xl py-4" />
                        </flux:field>
                    </div>
                </div>
            </section>
        </div>

        <!-- Right Column: Layout & Assets -->
        <div class="space-y-12">
            <!-- 3. Layout Presets -->
            <section class="bg-white rounded-[40px] border border-zinc-100 p-10 space-y-8 shadow-sm">
                <div>
                    <h3 class="text-lg font-bold text-black tracking-tight">Orchestrator</h3>
                    <p class="text-xs text-zinc-400 font-medium uppercase tracking-widest mt-1">Showroom Arrangement</p>
                </div>

                <div class="space-y-4">
                    @foreach($layouts as $key => $l)
                        <button 
                            wire:click="$set('layout', '{{ $key }}')"
                            class="w-full p-6 rounded-[25px] border-2 text-left transition-all {{ $layout === $key ? 'border-black bg-zinc-50' : 'border-zinc-50 hover:border-zinc-200' }}"
                        >
                            <p class="text-sm font-bold text-black">{{ $l['name'] }}</p>
                            <p class="text-[10px] text-zinc-400 font-medium mt-1">{{ $l['description'] }}</p>
                        </button>
                    @endforeach
                </div>
            </section>

            <!-- 4. Module Toggles -->
            <section class="bg-white rounded-[40px] border border-zinc-100 p-10 space-y-8 shadow-sm">
                <div>
                    <h3 class="text-lg font-bold text-black tracking-tight">Module Visibility</h3>
                    <p class="text-xs text-zinc-400 font-medium uppercase tracking-widest mt-1">Toggle Platform Features</p>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between items-center p-4 rounded-2xl bg-zinc-50">
                        <span class="text-[11px] font-black uppercase tracking-widest">Auction Spotlight</span>
                        <flux:checkbox wire:model="show_auctions" switch />
                    </div>
                    <div class="flex justify-between items-center p-4 rounded-2xl bg-zinc-50">
                        <span class="text-[11px] font-black uppercase tracking-widest">Comparison Engine</span>
                        <flux:checkbox wire:model="show_comparison" switch />
                    </div>
                    <div class="flex justify-between items-center p-4 rounded-2xl bg-zinc-50">
                        <span class="text-[11px] font-black uppercase tracking-widest">Inquiry Forms</span>
                        <flux:checkbox wire:model="show_inquiries" switch />
                    </div>
                </div>
            </section>

            <!-- 5. Logo Upload -->
            <section class="bg-white rounded-[40px] border border-zinc-100 p-10 space-y-8 shadow-sm">
                <div>
                    <h3 class="text-lg font-bold text-black tracking-tight">Brand Asset</h3>
                    <p class="text-xs text-zinc-400 font-medium uppercase tracking-widest mt-1">Official Showroom Logo</p>
                </div>

                <div class="space-y-6">
                    <div class="relative group aspect-video rounded-[30px] overflow-hidden bg-zinc-50 border-2 border-dashed border-zinc-100 flex flex-col items-center justify-center p-6 text-center hover:border-black transition-all">
                        @if ($logo)
                            <img src="{{ $logo->temporaryUrl() }}" class="absolute inset-0 w-full h-full object-contain p-8">
                        @elseif ($logo_path)
                            <img src="{{ Storage::url($logo_path) }}" class="absolute inset-0 w-full h-full object-contain p-8">
                        @else
                            <svg viewBox="0 0 24 24" fill="none" class="h-10 w-10 text-zinc-200 mb-4" stroke="currentColor" stroke-width="1.5"><path d="M12 16V6M12 6L8 10M12 6L16 10" stroke-linecap="round"/><path d="M5 15V17A2 2 0 0 0 7 19H17A2 2 0 0 0 19 17V15" stroke-linecap="round"/></svg>
                            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Click to upload SVG or PNG</p>
                        @endif
                        <input type="file" wire:model="logo" class="absolute inset-0 opacity-0 cursor-pointer">
                    </div>
                    @error('logo') <span class="text-red-600 text-[10px] font-bold">{{ $message }}</span> @enderror
                </div>
            </section>
        </div>
    </div>
</div>
