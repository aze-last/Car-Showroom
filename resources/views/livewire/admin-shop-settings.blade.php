@php
    use App\Models\Setting;
@endphp

<x-pages::settings.layout :heading="__('System Master')" :subheading="__('High-fidelity orchestration for the Gallery\'s global parameters.')">
    <div class="space-y-12 animate-showroom-fade-up">
        <!-- Tab Orchestrator -->
        <nav class="flex bg-zinc-50 p-1.5 rounded-2xl border border-zinc-100 ambient-shadow overflow-x-auto max-w-full">
            <button wire:click="$set('activeTab', 'identity')" class="px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $activeTab === 'identity' ? 'bg-white text-black shadow-sm' : 'text-zinc-400 hover:text-zinc-600' }}">Identity</button>
            <button wire:click="$set('activeTab', 'geography')" class="px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $activeTab === 'geography' ? 'bg-white text-black shadow-sm' : 'text-zinc-400 hover:text-zinc-600' }}">Geography</button>
            <button wire:click="$set('activeTab', 'socials')" class="px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $activeTab === 'socials' ? 'bg-white text-black shadow-sm' : 'text-zinc-400 hover:text-zinc-600' }}">Socials</button>
            <button wire:click="$set('activeTab', 'appearance')" class="px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $activeTab === 'appearance' ? 'bg-white text-black shadow-sm' : 'text-zinc-400 hover:text-zinc-600' }}">Appearance</button>
            <button wire:click="$set('activeTab', 'infrastructure')" class="px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $activeTab === 'infrastructure' ? 'bg-white text-black shadow-sm' : 'text-zinc-400 hover:text-zinc-600' }}">Infrastructure</button>
        </nav>

        <!-- Main Content Stage -->
        <div class="space-y-12">
            
            @if($activeTab === 'identity')
                <section class="bg-white rounded-[40px] p-8 md:p-12 border border-zinc-100 ambient-shadow space-y-12">
                    <div class="flex items-center gap-6 pb-8 border-b border-zinc-50">
                        <div class="h-14 w-14 rounded-[20px] bg-black text-white flex items-center justify-center">
                            <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-black tracking-tight">Institutional Identity</h3>
                            <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest mt-1">Foundational naming and branding</p>
                        </div>
                    </div>

                    <!-- Logo Settings Moved Here for Better Visibility -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 pt-4">
                        <div class="space-y-4">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest block">Institution Logo</label>
                            <div class="relative group">
                                <div class="border-2 border-dashed border-zinc-100 rounded-[30px] p-8 flex flex-col items-center justify-center text-center hover:bg-zinc-50 transition-colors cursor-pointer relative overflow-hidden">
                                    @if ($logo instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile && $logo->isPreviewable())
                                        <img src="{{ $logo->temporaryUrl() }}" class="h-12 w-auto object-contain">
                                    @elseif ($current_logo_url)
                                        <img src="{{ $current_logo_url }}" class="h-12 w-auto object-contain">
                                    @else
                                        <svg viewBox="0 0 24 24" fill="none" class="h-8 w-8 text-zinc-200" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                    @endif
                                    <input type="file" wire:model="logo" class="absolute inset-0 opacity-0 cursor-pointer">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest block">Exhibition Wordmark</label>
                            <div class="relative group">
                                <div class="border-2 border-dashed border-zinc-100 rounded-[30px] p-8 flex flex-col items-center justify-center text-center hover:bg-zinc-50 transition-colors cursor-pointer relative overflow-hidden">
                                    @if ($design_logo instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile && $design_logo->isPreviewable())
                                        <img src="{{ $design_logo->temporaryUrl() }}" class="h-12 w-auto object-contain">
                                    @elseif ($current_design_logo_url)
                                        <img src="{{ $current_design_logo_url }}" class="h-12 w-auto object-contain">
                                    @else
                                        <svg viewBox="0 0 24 24" fill="none" class="h-8 w-8 text-zinc-200" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                    @endif
                                    <input type="file" wire:model="design_logo" class="absolute inset-0 opacity-0 cursor-pointer">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 pt-8 border-t border-zinc-50">
                        <div class="space-y-4">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.2em] block">Legal Entity Name</label>
                            <input wire:model="legal_name" type="text" class="w-full bg-zinc-50 border-none rounded-2xl py-4 px-6 font-bold text-sm text-zinc-900 focus:ring-2 focus:ring-black transition-all" placeholder="Legal Entity Name">
                        </div>
                        <div class="space-y-4">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.2em] block">Trading As (DBA)</label>
                            <input wire:model="dba_name" type="text" class="w-full bg-zinc-50 border-none rounded-2xl py-4 px-6 font-bold text-sm text-zinc-900 focus:ring-2 focus:ring-black transition-all" placeholder="The Gallery">
                        </div>
                        <div class="space-y-4 md:col-span-2">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.2em] block">Public Contact Line</label>
                            <input wire:model="shop_phone" type="text" class="w-full bg-zinc-50 border-none rounded-2xl py-4 px-6 font-bold text-sm text-zinc-900 focus:ring-2 focus:ring-black transition-all" placeholder="+63 XXX XXX XXXX">
                        </div>
                    </div>

                    <div class="pt-12 border-t border-zinc-50 space-y-8">
                        <h4 class="text-[10px] font-bold text-black uppercase tracking-[0.4em]">Inquiry Routing Architecture</h4>
                        <div class="grid grid-cols-1 gap-6">
                            <div class="bg-zinc-50 p-6 rounded-3xl flex items-center gap-6 group hover:bg-zinc-100 transition-colors">
                                <div class="h-12 w-12 rounded-2xl bg-white flex items-center justify-center text-zinc-400 group-hover:text-black transition-colors shadow-sm">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2.5"><path d="M4 8.5L12 4L20 8.5V15.5L12 20L4 15.5V8.5Z" stroke-linejoin="round"/></svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-[11px] font-bold text-black uppercase tracking-widest">Sales Acquisitions</p>
                                    <input wire:model="sales_inquiry_email" type="email" class="w-full bg-transparent border-0 border-b border-zinc-200 px-0 py-2 text-sm font-bold text-zinc-900 focus:ring-0 focus:border-black transition-all">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            @if($activeTab === 'geography')
                <section class="bg-white rounded-[40px] p-8 md:p-12 border border-zinc-100 ambient-shadow space-y-12">
                    <div class="flex items-center gap-6 pb-8 border-b border-zinc-50">
                        <div class="h-14 w-14 rounded-[20px] bg-black text-white flex items-center justify-center">
                            <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-black tracking-tight">Geographic Presence</h3>
                            <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest mt-1">Physical landmark and GPS triangulation</p>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div class="space-y-4">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.2em] block">Flagship Address</label>
                            <input wire:model="shop_address" type="text" class="w-full bg-zinc-50 border-none rounded-2xl py-4 px-6 font-bold text-sm text-zinc-900 focus:ring-2 focus:ring-black transition-all" placeholder="Full Street Address">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-4">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.2em] block">City</label>
                                <input wire:model="shop_city" type="text" class="w-full bg-zinc-50 border-none rounded-2xl py-4 px-6 font-bold text-sm text-zinc-900 focus:ring-2 focus:ring-black transition-all">
                            </div>
                            <div class="space-y-4">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.2em] block">State/Region</label>
                                <input wire:model="shop_state" type="text" class="w-full bg-zinc-50 border-none rounded-2xl py-4 px-6 font-bold text-sm text-zinc-900 focus:ring-2 focus:ring-black transition-all">
                            </div>
                            <div class="space-y-4">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.2em] block">Zip Code</label>
                                <input wire:model="shop_postal_code" type="text" class="w-full bg-zinc-50 border-none rounded-2xl py-4 px-6 font-bold text-sm text-zinc-900 focus:ring-2 focus:ring-black transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="pt-12 border-t border-zinc-50 space-y-8">
                        <div class="flex justify-between items-end">
                            <div>
                                <h4 class="text-[10px] font-bold text-black uppercase tracking-[0.4em]">Precision Coordinates</h4>
                                <p class="text-[11px] font-medium text-zinc-400 mt-1">Drives the interactive map on the About page</p>
                            </div>
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $map_latitude }},{{ $map_longitude }}" target="_blank" class="px-6 py-3 bg-zinc-100 text-black rounded-full font-bold text-[9px] uppercase tracking-widest hover:bg-zinc-200 transition-all flex items-center gap-2">
                                <svg viewBox="0 0 24 24" fill="none" class="h-3 w-3" stroke="currentColor" stroke-width="3"><path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z"/><circle cx="12" cy="10" r="3"/></svg>
                                Test Routing
                            </a>
                        </div>

                        <div class="grid grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.2em] block">Latitude</label>
                                <input wire:model.live.debounce.500ms="map_latitude" type="text" class="w-full bg-zinc-50 border-none rounded-2xl py-4 px-6 font-mono font-bold text-sm text-zinc-900 focus:ring-2 focus:ring-black transition-all">
                            </div>
                            <div class="space-y-4">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.2em] block">Longitude</label>
                                <input wire:model.live.debounce.500ms="map_longitude" type="text" class="w-full bg-zinc-50 border-none rounded-2xl py-4 px-6 font-mono font-bold text-sm text-zinc-900 focus:ring-2 focus:ring-black transition-all">
                            </div>
                        </div>

                        <div wire:ignore class="h-64 rounded-[32px] overflow-hidden border border-zinc-100 ambient-shadow relative z-0" id="admin-map-preview"></div>
                    </div>

                    <script>
                        (function() {
                            let map, marker;
                            const initAdminMap = () => {
                                if (typeof L === 'undefined') {
                                    setTimeout(initAdminMap, 200);
                                    return;
                                }
                                const container = document.getElementById('admin-map-preview');
                                if (!container || container._leaflet_id) return;

                                map = L.map('admin-map-preview').setView([@js($map_latitude), @js($map_longitude)], 15);
                                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                                marker = L.marker([@js($map_latitude), @js($map_longitude)]).addTo(map);
                            };

                            initAdminMap();
                            document.addEventListener('livewire:navigated', initAdminMap);

                            window.addEventListener('update-map', event => {
                                const lat = event.detail.lat;
                                const lng = event.detail.lng;
                                if (map && marker) {
                                    const pos = [lat, lng];
                                    marker.setLatLng(pos);
                                    map.panTo(pos);
                                }
                            });
                        })();
                    </script>
                </section>
            @endif

            @if($activeTab === 'socials')
                <section class="bg-white rounded-[40px] p-8 md:p-12 border border-zinc-100 ambient-shadow space-y-12">
                    <div class="flex items-center gap-6 pb-8 border-b border-zinc-50">
                        <div class="h-14 w-14 rounded-[20px] bg-black text-white flex items-center justify-center">
                            <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2.5"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-black tracking-tight">Social Narratives</h3>
                            <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest mt-1">Digital engagement channels</p>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div class="flex items-center gap-8 p-6 md:p-8 rounded-[32px] bg-zinc-50 border border-zinc-100 group">
                            <div class="h-14 w-14 rounded-2xl bg-white flex items-center justify-center text-black shadow-sm group-hover:scale-110 transition-transform">
                                <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                            </div>
                            <div class="flex-1">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest block mb-2">Facebook URL</label>
                                <input wire:model="facebook_url" type="text" class="w-full bg-transparent border-0 border-b border-zinc-200 px-0 py-2 text-sm font-bold text-zinc-900 focus:ring-0 focus:border-black" placeholder="https://facebook.com/...">
                            </div>
                        </div>

                        <div class="flex items-center gap-8 p-6 md:p-8 rounded-[32px] bg-zinc-50 border border-zinc-100 group">
                            <div class="h-14 w-14 rounded-2xl bg-white flex items-center justify-center text-black shadow-sm group-hover:scale-110 transition-transform">
                                <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                            </div>
                            <div class="flex-1">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest block mb-2">Instagram URL</label>
                                <input wire:model="instagram_url" type="text" class="w-full bg-transparent border-0 border-b border-zinc-200 px-0 py-2 text-sm font-bold text-zinc-900 focus:ring-0 focus:border-black" placeholder="https://instagram.com/...">
                            </div>
                        </div>

                        <div class="flex items-center gap-8 p-6 md:p-8 rounded-[32px] bg-zinc-50 border border-zinc-100 group">
                            <div class="h-14 w-14 rounded-2xl bg-white flex items-center justify-center text-black shadow-sm group-hover:scale-110 transition-transform">
                                <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/></svg>
                            </div>
                            <div class="flex-1">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest block mb-2">TikTok URL</label>
                                <input wire:model="tiktok_url" type="text" class="w-full bg-transparent border-0 border-b border-zinc-200 px-0 py-2 text-sm font-bold text-zinc-900 focus:ring-0 focus:border-black" placeholder="https://tiktok.com/@...">
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            @if($activeTab === 'appearance')
                <section class="bg-white rounded-[40px] p-8 md:p-12 border border-zinc-100 ambient-shadow space-y-12">
                    <div class="flex items-center gap-6 pb-8 border-b border-zinc-50">
                        <div class="h-14 w-14 rounded-[20px] bg-black text-white flex items-center justify-center">
                            <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2.5"><path d="M12 3V5M12 19V21M4.22 4.22L5.64 5.64M18.36 18.36L19.78 19.78M1 12H3M21 12H23M4.22 19.78L5.64 18.36M18.36 5.64L19.78 4.22" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="4"/></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-black tracking-tight">Gallery Appearance</h3>
                            <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest mt-1">Aesthetic orchestration and layout presets</p>
                        </div>
                    </div>

                    <div class="space-y-12">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                            <div class="space-y-6">
                                <label class="text-[10px] font-bold text-black uppercase tracking-[0.4em]">Color Narrative</label>
                                <div class="grid grid-cols-5 gap-3">
                                    @foreach($palettes as $key => $p)
                                        <button 
                                            wire:click="$set('palette', '{{ $key }}')"
                                            class="h-12 w-full rounded-xl border-2 transition-all flex items-center justify-center {{ $palette === $key ? 'border-black scale-110 shadow-lg' : 'border-transparent hover:border-zinc-200' }}"
                                            style="background-color: {{ $p['primary'] }}"
                                            title="{{ ucfirst($key) }}"
                                        >
                                            @if($palette === $key)
                                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-white" stroke="currentColor" stroke-width="4"><polyline points="20 6 9 17 4 12"/></svg>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <div class="space-y-6">
                                <label class="text-[10px] font-bold text-black uppercase tracking-[0.4em]">Exhibition Layout</label>
                                <div class="grid grid-cols-1 gap-3">
                                    @foreach($layouts as $key => $l)
                                        <button 
                                            wire:click="$set('layout_preset', '{{ $key }}')"
                                            class="w-full p-4 rounded-2xl border-2 text-left transition-all {{ $layout_preset === $key ? 'border-black bg-zinc-50' : 'border-zinc-100 hover:border-zinc-200' }}"
                                        >
                                            <p class="text-xs font-black uppercase tracking-widest {{ $layout_preset === $key ? 'text-black' : 'text-zinc-400' }}">{{ $l['name'] }}</p>
                                            <p class="text-[10px] text-zinc-400 mt-1">{{ $l['description'] }}</p>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="pt-12 border-t border-zinc-50 space-y-12">
                            <div class="space-y-6">
                                <label class="text-[10px] font-bold text-black uppercase tracking-[0.4em]">Hero Selection</label>
                                <select wire:model="hero_unit_id" class="w-full bg-zinc-50 border-none rounded-2xl py-4 px-6 font-bold text-sm text-zinc-900 focus:ring-2 focus:ring-black transition-all">
                                    <option value="">No Featured Unit</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->year }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                                <div class="space-y-4">
                                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.2em] block">Hero Headline</label>
                                    <input wire:model="hero_headline" type="text" class="w-full bg-zinc-50 border-none rounded-2xl py-4 px-6 font-bold text-sm text-zinc-900 focus:ring-2 focus:ring-black transition-all">
                                </div>
                                <div class="space-y-4">
                                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.2em] block">Hero Subtitle</label>
                                    <input wire:model="hero_subtitle" type="text" class="w-full bg-zinc-50 border-none rounded-2xl py-4 px-6 font-bold text-sm text-zinc-900 focus:ring-2 focus:ring-black transition-all">
                                </div>
                            </div>
                        </div>

                        <div class="pt-12 border-t border-zinc-50 space-y-8">
                            <h4 class="text-[10px] font-bold text-black uppercase tracking-[0.4em]">Feature Toggles</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <label class="flex items-center gap-4 p-6 rounded-[32px] bg-zinc-50 cursor-pointer hover:bg-zinc-100 transition-colors">
                                    <input type="checkbox" wire:model="show_auctions" class="h-6 w-6 rounded-lg border-zinc-200 text-black focus:ring-black">
                                    <span class="text-[11px] font-bold uppercase tracking-widest text-zinc-900">Live Auctions</span>
                                </label>
                                <label class="flex items-center gap-4 p-6 rounded-[32px] bg-zinc-50 cursor-pointer hover:bg-zinc-100 transition-colors">
                                    <input type="checkbox" wire:model="show_comparison" class="h-6 w-6 rounded-lg border-zinc-200 text-black focus:ring-black">
                                    <span class="text-[11px] font-bold uppercase tracking-widest text-zinc-900">Comparison Tool</span>
                                </label>
                                <label class="flex items-center gap-4 p-6 rounded-[32px] bg-zinc-50 cursor-pointer hover:bg-zinc-100 transition-colors">
                                    <input type="checkbox" wire:model="show_inquiries" class="h-6 w-6 rounded-lg border-zinc-200 text-black focus:ring-black">
                                    <span class="text-[11px] font-bold uppercase tracking-widest text-zinc-900">Lead Capture</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            @if($activeTab === 'infrastructure')
                <section class="bg-white rounded-[40px] p-8 md:p-12 border border-zinc-100 ambient-shadow space-y-12">
                    <div class="flex items-center gap-6 pb-8 border-b border-zinc-50">
                        <div class="h-14 w-14 rounded-[20px] bg-black text-white flex items-center justify-center">
                            <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-black tracking-tight">System Infrastructure</h3>
                            <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest mt-1">Cloud storage and aesthetic parameters</p>
                        </div>
                    </div>

                    <div class="space-y-12">
                        <div class="grid grid-cols-2 gap-12 pt-12 border-t border-zinc-50">
                            <div class="space-y-4">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.2em] block">Storage Bucket</label>
                                <div class="p-5 rounded-2xl bg-zinc-50 border border-zinc-100 text-xs font-mono text-zinc-900">{{ $s3_bucket }}</div>
                            </div>
                            <div class="space-y-4">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.2em] block">Storage Region</label>
                                <div class="p-5 rounded-2xl bg-zinc-50 border border-zinc-100 text-xs font-mono text-zinc-900">{{ $s3_region }}</div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            <div class="pt-10 flex justify-end">
                <button 
                    wire:click="save" 
                    wire:loading.attr="disabled" 
                    class="bg-black text-white px-12 py-5 rounded-2xl font-black uppercase tracking-[0.3em] text-[11px] shadow-xl hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-3 group"
                >
                    <span wire:loading.remove wire:target="save">Commit System Update</span>
                    <span wire:loading wire:target="save">Updating...</span>
                    <svg wire:loading.remove wire:target="save" viewBox="0 0 24 24" fill="none" class="h-4 w-4 transform group-hover:translate-x-1 transition-transform" stroke="currentColor" stroke-width="3"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>
            </div>

            @if (session('status'))
                <div class="mt-6 p-6 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 text-[10px] font-bold uppercase tracking-widest text-center animate-showroom-fade-up">
                    {{ session('status') }}
                </div>
            @endif
        </div>
    </div>
</x-pages::settings.layout>
