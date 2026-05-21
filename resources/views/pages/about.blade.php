@php
    use App\Models\Setting;
@endphp

<x-layouts.public-showroom :title="Setting::get('shop_name', 'Car Showroom') . ' | About'">
    <div class="space-y-24 py-12">
        <!-- Hero Section -->
        <section class="max-w-3xl mx-auto text-center space-y-6">
            <h1 class="text-4xl font-black tracking-tighter sm:text-6xl text-zinc-900 uppercase">
                About Our <span class="text-zinc-400">Showroom</span>
            </h1>
            <p class="text-lg font-bold text-zinc-500 leading-relaxed">
                We provide the finest selection of premium vehicles, from classic cars to modern motorcycles. Our mission is to deliver excellence in every unit we showcase.
            </p>
            <div class="flex justify-center pt-4">
                <div class="h-1.5 w-12 bg-zinc-900"></div>
            </div>
        </section>

        <!-- Main Content -->
        <div class="grid gap-16 lg:grid-cols-[1.5fr_1fr]">
            <!-- Map Section -->
            <section class="space-y-8">
                <header>
                    <h2 class="text-xs font-black uppercase tracking-[0.4em] text-zinc-900">Our Location</h2>
                    <p class="mt-2 text-sm font-bold text-zinc-400">{{ Setting::get('shop_address') }}</p>
                </header>
                
                <div 
                    id="map"
                    class="aspect-video w-full overflow-hidden rounded-[40px] border border-zinc-100 bg-zinc-50 shadow-2xl shadow-zinc-200/50 z-0"
                    style="height: 400px;"
                ></div>

                <script>
                    (function() {
                        const initMap = () => {
                            if (typeof L === 'undefined') {
                                console.log('Waiting for Leaflet...');
                                setTimeout(initMap, 200);
                                return;
                            }

                            const container = document.getElementById('map');
                            if (!container || container._leaflet_id) return;

                            const lat = parseFloat("{{ Setting::get('map_latitude', '14.5995') }}");
                            const lng = parseFloat("{{ Setting::get('map_longitude', '120.9842') }}");
                            const shopName = "{{ Setting::get('shop_name', 'Our Showroom') }}";

                            const map = L.map('map').setView([lat, lng], 15);

                            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                maxZoom: 19,
                                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                            }).addTo(map);

                            L.marker([lat, lng]).addTo(map)
                                .bindPopup('<b>' + shopName + '</b><br>Visit our showroom.')
                                .openPopup();
                            
                            setTimeout(() => {
                                map.invalidateSize();
                            }, 300);
                        };

                        if (document.readyState === 'loading') {
                            document.addEventListener('DOMContentLoaded', initMap);
                        } else {
                            initMap();
                        }

                        document.addEventListener('livewire:navigated', initMap);
                    })();
                </script>
            </section>

            <!-- Contact & Socials -->
            <section class="space-y-12 lg:pt-16">
                <!-- Social Media -->
                <div class="space-y-8">
                    <h3 class="text-xs font-black uppercase tracking-[0.4em] text-zinc-900">Connect With Us</h3>
                    
                    <div class="grid grid-cols-1 gap-4">
                        @if($facebook = Setting::get('facebook_url'))
                            <a href="{{ $facebook }}" target="_blank" class="group flex items-center gap-6 rounded-3xl border border-zinc-100 bg-white p-6 transition-all hover:-translate-y-1 hover:shadow-xl">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-zinc-50 text-zinc-900 transition-colors group-hover:bg-zinc-900 group-hover:text-white">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-black uppercase tracking-widest text-zinc-900">Facebook</p>
                                    <p class="text-[10px] font-bold text-zinc-400">Follow our latest news</p>
                                </div>
                            </a>
                        @endif

                        @if($instagram = Setting::get('instagram_url'))
                            <a href="{{ $instagram }}" target="_blank" class="group flex items-center gap-6 rounded-3xl border border-zinc-100 bg-white p-6 transition-all hover:-translate-y-1 hover:shadow-xl">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-zinc-50 text-zinc-900 transition-colors group-hover:bg-zinc-900 group-hover:text-white">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-black uppercase tracking-widest text-zinc-900">Instagram</p>
                                    <p class="text-[10px] font-bold text-zinc-400">See our gallery</p>
                                </div>
                            </a>
                        @endif

                        @if($tiktok = Setting::get('tiktok_url'))
                            <a href="{{ $tiktok }}" target="_blank" class="group flex items-center gap-6 rounded-3xl border border-zinc-100 bg-white p-6 transition-all hover:-translate-y-1 hover:shadow-xl">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-zinc-50 text-zinc-900 transition-colors group-hover:bg-zinc-900 group-hover:text-white">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-black uppercase tracking-widest text-zinc-900">TikTok</p>
                                    <p class="text-[10px] font-bold text-zinc-400">Watch our videos</p>
                                </div>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Contact Detail -->
                <div class="space-y-6 pt-8 border-t border-zinc-50">
                    <div class="space-y-2">
                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Direct Contact</p>
                        <p class="text-2xl font-black text-zinc-900">{{ Setting::get('shop_phone') }}</p>
                    </div>
                    <div class="space-y-2">
                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Email Us</p>
                        <p class="text-lg font-bold text-zinc-900 underline underline-offset-4">{{ Setting::get('shop_email') }}</p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-layouts.public-showroom>
