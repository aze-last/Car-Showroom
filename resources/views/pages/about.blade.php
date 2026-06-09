@php
    use App\Models\Setting;
@endphp

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'AutoDealer',
        'name' => Setting::get('shop_name', 'The Gallery'),
        'image' => asset('favicon.svg'),
        'telephone' => Setting::get('shop_phone', ''),
        'email' => Setting::get('shop_email', ''),
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => Setting::get('shop_address', ''),
            'addressCountry' => 'PH'
        ]
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

<x-layouts.public-showroom :title="Setting::get('shop_name', 'The Gallery') . ' | About'">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-32 animate-showroom-fade-up">
        <!-- Editorial Hero -->
        <section class="max-w-4xl mx-auto text-center space-y-8">
            <h1 class="text-6xl sm:text-8xl font-bold tracking-tighter text-black leading-none uppercase">
                The <span class="text-zinc-300">Curators</span>
            </h1>
            <p class="text-xl font-medium text-zinc-500 leading-relaxed max-w-2xl mx-auto">
                We bridge the gap between architectural excellence and automotive performance. Our gallery serves as a sanctuary for the world's most distinguished vehicles.
            </p>
            <div class="flex justify-center pt-8">
                <div class="h-[2px] w-24 bg-black"></div>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-[1.5fr_1fr] gap-24">
            <!-- Map Section -->
            <section class="space-y-12">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                    <div class="space-y-4">
                        <h2 class="text-[12px] font-bold uppercase tracking-[0.4em] text-black">The Landmark</h2>
                        <p class="text-lg font-bold text-zinc-400 leading-tight max-w-md">{{ Setting::get('shop_address') }}</p>
                    </div>
                    <a href="https://www.google.com/maps/dir/?api=1&destination={{ Setting::get('map_latitude', '14.5995') }},{{ Setting::get('map_longitude', '120.9842') }}" target="_blank" class="px-8 py-4 bg-black text-white rounded-full font-bold text-[11px] uppercase tracking-widest hover:scale-105 transition-all shadow-xl flex items-center gap-3">
                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="3"><path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z"/><circle cx="12" cy="10" r="3"/></svg>
                        Get Directions
                    </a>
                </div>
                
                <div 
                    id="map"
                    class="aspect-video w-full overflow-hidden rounded-[48px] bg-gallery-surface-low border border-gallery-outline/20 ambient-shadow z-0"
                    style="height: 500px;"
                ></div>

                <script>
                    (function() {
                        const initMap = () => {
                            if (typeof L === 'undefined') {
                                setTimeout(initMap, 200);
                                return;
                            }

                            const container = document.getElementById('map');
                            if (!container || container._leaflet_id) return;

                            const lat = parseFloat("{{ Setting::get('map_latitude', '14.5995') }}");
                            const lng = parseFloat("{{ Setting::get('map_longitude', '120.9842') }}");
                            const shopName = "{{ Setting::get('shop_name', 'The Gallery') }}";

                            const map = L.map('map').setView([lat, lng], 15);

                            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                maxZoom: 19,
                                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                            }).addTo(map);

                            L.marker([lat, lng]).addTo(map)
                                .bindPopup('<b class="font-bold">' + shopName + '</b><br>Curated Excellence.')
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
            <section class="space-y-16 flex flex-col justify-center">
                <div class="space-y-12">
                    <h3 class="text-[12px] font-bold uppercase tracking-[0.4em] text-black">Connect With Us</h3>
                    
                    <div class="grid grid-cols-1 gap-6">
                        @if($facebook = Setting::get('facebook_url'))
                            <a href="{{ $facebook }}" target="_blank" class="group flex items-center gap-8 rounded-[32px] border border-gallery-outline/20 bg-white p-8 transition-all hover:-translate-y-2 ambient-shadow">
                                <div class="flex h-14 w-14 items-center justify-center rounded-[20px] bg-gallery-surface-low text-black transition-colors group-hover:bg-black group-hover:text-white">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[12px] font-bold uppercase tracking-widest text-black">Facebook</p>
                                    <p class="text-[11px] font-medium text-zinc-400">Join the conversation</p>
                                </div>
                            </a>
                        @endif

                        @if($instagram = Setting::get('instagram_url'))
                            <a href="{{ $instagram }}" target="_blank" class="group flex items-center gap-8 rounded-[32px] border border-gallery-outline/20 bg-white p-8 transition-all hover:-translate-y-2 ambient-shadow">
                                <div class="flex h-14 w-14 items-center justify-center rounded-[20px] bg-gallery-surface-low text-black transition-colors group-hover:bg-black group-hover:text-white">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[12px] font-bold uppercase tracking-widest text-black">Instagram</p>
                                    <p class="text-[11px] font-medium text-zinc-400">Visual narratives</p>
                                </div>
                            </a>
                        @endif

                        @if($tiktok = Setting::get('tiktok_url'))
                            <a href="{{ $tiktok }}" target="_blank" class="group flex items-center gap-8 rounded-[32px] border border-gallery-outline/20 bg-white p-8 transition-all hover:-translate-y-2 ambient-shadow">
                                <div class="flex h-14 w-14 items-center justify-center rounded-[20px] bg-gallery-surface-low text-black transition-colors group-hover:bg-black group-hover:text-white">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/></svg>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[12px] font-bold uppercase tracking-widest text-black">TikTok</p>
                                    <p class="text-[11px] font-medium text-zinc-400">Cinematic motion</p>
                                </div>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="space-y-10 pt-12 border-t border-gallery-outline/10">
                    <div class="space-y-2">
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-zinc-400">Curator Line</p>
                        <p class="text-4xl font-bold tracking-tight text-black leading-none">{{ Setting::get('shop_phone') }}</p>
                    </div>
                    <div class="space-y-2">
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-zinc-400">Digital Inquiries</p>
                        <p class="text-xl font-bold text-black underline underline-offset-8 decoration-zinc-200 decoration-2 hover:decoration-black transition-all">{{ Setting::get('shop_email') }}</p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-layouts.public-showroom>
