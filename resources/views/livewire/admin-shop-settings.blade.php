@php
    use App\Models\Setting;
@endphp

<div class="space-y-12 animate-showroom-fade-up">
    <!-- Header -->
    <header class="mb-12">
        <h2 class="text-5xl font-bold tracking-tighter text-black mb-2">Global Settings</h2>
        <p class="text-sm font-medium text-zinc-400">Manage foundational configurations and operational parameters for The Gallery.</p>
    </header>

    <!-- Settings Bento Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Dealership Info Card -->
        <section class="lg:col-span-8 bg-white rounded-[40px] p-10 border border-gallery-outline/20 ambient-shadow hover-lift">
            <div class="flex items-center gap-4 mb-10 pb-6 border-b border-gallery-outline/10">
                <div class="h-10 w-10 rounded-full bg-gallery-surface-low flex items-center justify-center text-black">
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <h3 class="text-[12px] font-bold text-black uppercase tracking-[0.4em]">Dealership Profile</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10"> 
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest block">Legal Entity Name</label>
                    <input wire:model="legal_name" type="text" class="w-full bg-transparent border-0 border-b border-gallery-outline/30 rounded-none px-0 py-3 text-base font-bold text-black focus:ring-0 focus:border-black transition-colors" placeholder="Legal Entity Name">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest block">Trading As (DBA)</label>
                    <input wire:model="dba_name" type="text" class="w-full bg-transparent border-0 border-b border-gallery-outline/30 rounded-none px-0 py-3 text-base font-bold text-black focus:ring-0 focus:border-black transition-colors" placeholder="The Gallery">      
                </div>
            </div>

            <div class="space-y-2 pt-10">
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest block">Headquarters Address</label>
                <input wire:model="shop_address" type="text" class="w-full bg-transparent border-0 border-b border-gallery-outline/30 rounded-none px-0 py-3 text-base font-bold text-black focus:ring-0 focus:border-black transition-colors" placeholder="123 Prestige Boulevard">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 pt-10">
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest block">City</label>        
                    <input wire:model="shop_city" type="text" class="w-full bg-transparent border-0 border-b border-gallery-outline/30 rounded-none px-0 py-3 text-base font-bold text-black focus:ring-0 focus:border-black transition-colors" placeholder="Beverly Hills">    
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest block">State</label>
                    <input wire:model="shop_state" type="text" class="w-full bg-transparent border-0 border-b border-gallery-outline/30 rounded-none px-0 py-3 text-base font-bold text-black focus:ring-0 focus:border-black transition-colors" placeholder="CA">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest block">Postal Code</label> 
                    <input wire:model="shop_postal_code" type="text" class="w-full bg-transparent border-0 border-b border-gallery-outline/30 rounded-none px-0 py-3 text-base font-bold text-black focus:ring-0 focus:border-black transition-colors" placeholder="90210">
                </div>
            </div>
        </section>

        <!-- Storage & S3 Card -->
        <section class="lg:col-span-4 bg-gallery-surface-low rounded-[40px] p-10 border border-gallery-outline/20 ambient-shadow hover-lift flex flex-col">       
            <div class="flex items-center gap-4 mb-10 pb-6 border-b border-gallery-outline/10">
                <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center text-black">
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                </div>
                <h3 class="text-[12px] font-bold text-black uppercase tracking-[0.4em]">Asset Storage</h3> 
            </div>

            <div class="flex-1 space-y-10">
                <div class="flex items-center justify-between">     
                    <div>
                        <p class="text-sm font-bold text-black tracking-tight">Amazon S3 Cloud</p>
                        <p class="text-[11px] font-medium text-zinc-400 mt-1 uppercase tracking-widest">High-res Content</p>
                    </div>
                    <span class="bg-black text-white px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest flex items-center gap-1.5 shadow-xl">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Active
                    </span>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest block">Bucket Name</label> 
                    <input type="text" readonly value="{{ $s3_bucket }}" class="w-full bg-white/50 border-0 border-b border-gallery-outline/20 rounded-t-2xl px-4 py-3 text-sm font-medium text-zinc-500 cursor-not-allowed">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest block">Deployment Region</label>      
                    <div class="w-full bg-transparent border-0 border-b border-gallery-outline/30 px-0 py-3 text-base font-bold text-black uppercase tracking-widest text-xs">
                        {{ $s3_region }}
                    </div>
                </div>
            </div>

            <div class="mt-12">
                <button type="button" class="w-full h-14 border border-gallery-outline/30 rounded-2xl text-[10px] font-bold uppercase tracking-[0.2em] text-zinc-500 hover:text-black hover:border-black transition-all">
                    Configure IAM Vault
                </button>
            </div>
        </section>

        <!-- Brand Identity Card -->
        <section class="lg:col-span-5 bg-white rounded-[40px] p-10 border border-gallery-outline/20 ambient-shadow hover-lift">
            <div class="flex items-center gap-4 mb-10 pb-6 border-b border-gallery-outline/10">
                <div class="h-10 w-10 rounded-full bg-gallery-surface-low flex items-center justify-center text-black">
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M14.31 8l5.74 9.94M9.69 8h11.48M7.38 12l5.74-9.94M9.69 16L3.95 6.06M14.31 16H2.83m13.79-4l-5.74 9.94"/></svg>
                </div>
                <h3 class="text-[12px] font-bold text-black uppercase tracking-[0.4em]">Brand Identity</h3>
            </div>

            <div class="space-y-12">   
                <div>
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest block mb-6">Primary Wordmark</label>
                    <div class="relative group">
                        <div class="border-2 border-dashed border-gallery-outline/20 rounded-[32px] p-10 flex flex-col items-center justify-center text-center hover:bg-gallery-surface-low transition-colors cursor-pointer relative overflow-hidden">
                            @if ($logo instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile && $logo->isPreviewable())
                                <img src="{{ $logo->temporaryUrl() }}" class="h-16 w-auto object-contain">
                            @elseif ($current_logo_url)
                                <img src="{{ $current_logo_url }}" class="h-16 w-auto object-contain">
                            @else
                                <svg viewBox="0 0 24 24" fill="none" class="h-10 w-10 text-zinc-200 mb-4" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                <p class="text-[11px] font-bold text-black uppercase tracking-widest">Upload Master SVG</p>
                                <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-[0.2em] mt-2">Vector preferred (Max 2MB)</p>
                            @endif
                            <input type="file" wire:model="logo" class="absolute inset-0 opacity-0 cursor-pointer">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest block">Primary Palette</label>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full border border-gallery-outline/20 shadow-inner" style="background-color: {{ $primary_color }}"></div>        
                            <input wire:model="primary_color" type="text" class="w-full bg-transparent border-0 border-b border-gallery-outline/30 rounded-none px-0 py-2 text-xs font-bold text-black uppercase tracking-widest focus:ring-0 focus:border-black">
                        </div>
                    </div>
                    <div class="space-y-4">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest block">Accent Tone</label>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full border border-gallery-outline/20 shadow-inner" style="background-color: {{ $accent_tone }}"></div>   
                            <input wire:model="accent_tone" type="text" class="w-full bg-transparent border-0 border-b border-gallery-outline/30 rounded-none px-0 py-2 text-xs font-bold text-black uppercase tracking-widest focus:ring-0 focus:border-black">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Routing Card -->
        <section class="lg:col-span-7 bg-white rounded-[40px] p-10 border border-gallery-outline/20 ambient-shadow hover-lift">
            <div class="flex items-center gap-4 mb-10 pb-6 border-b border-gallery-outline/10">
                <div class="h-10 w-10 rounded-full bg-gallery-surface-low flex items-center justify-center text-black">
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                </div>
                <h3 class="text-[12px] font-bold text-black uppercase tracking-[0.4em]">Inquiry Routing</h3>
            </div>

            <div class="space-y-10">   
                <div class="flex items-start gap-6 p-6 rounded-[32px] hover:bg-gallery-surface-low transition-colors group">
                    <div class="mt-1 text-zinc-300 group-hover:text-black transition-colors">
                        <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2.5"><path d="M4 8.5L12 4L20 8.5V15.5L12 20L4 15.5V8.5Z" stroke-linejoin="round"/></svg>
                    </div>
                    <div class="flex-1">      
                        <p class="text-sm font-bold text-black tracking-tight uppercase tracking-widest text-[11px]">Sales Acquisitions</p>
                        <p class="text-[11px] font-medium text-zinc-400 mt-1">Default routing for vehicle inventory interest.</p>
                        <input wire:model="sales_inquiry_email" class="w-full max-w-sm mt-4 bg-transparent border-0 border-b border-gallery-outline/30 rounded-none px-0 py-2 text-sm font-bold text-black focus:ring-0 focus:border-black" type="email">
                    </div>
                </div>

                <div class="flex items-start gap-6 p-6 rounded-[32px] hover:bg-gallery-surface-low transition-colors group">
                    <div class="mt-1 text-zinc-300 group-hover:text-black transition-colors">
                        <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2.5"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                    </div>
                    <div class="flex-1">      
                        <p class="text-sm font-bold text-black tracking-tight uppercase tracking-widest text-[11px]">Concierge & Maintenance</p>
                        <p class="text-[11px] font-medium text-zinc-400 mt-1">Post-sale curation and vehicle care requests.</p>       
                        <input wire:model="service_inquiry_email" class="w-full max-w-sm mt-4 bg-transparent border-0 border-b border-gallery-outline/30 rounded-none px-0 py-2 text-sm font-bold text-black focus:ring-0 focus:border-black" type="email">
                    </div>
                </div>

                <div class="flex items-start gap-6 p-6 rounded-[32px] hover:bg-gallery-surface-low transition-colors group">
                    <div class="mt-1 text-zinc-300 group-hover:text-black transition-colors">
                        <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <div class="flex-1">      
                        <p class="text-sm font-bold text-black tracking-tight uppercase tracking-widest text-[11px]">Privacy & Compliance</p>
                        <p class="text-[11px] font-medium text-zinc-400 mt-1">Legal notices and secure vendor relations.</p>
                        <input wire:model="legal_inquiry_email" class="w-full max-w-sm mt-4 bg-transparent border-0 border-b border-gallery-outline/30 rounded-none px-0 py-2 text-sm font-bold text-black focus:ring-0 focus:border-black" type="email">
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Final Save Console -->
    <div class="mt-12 py-10 border-t border-gallery-outline/10 flex flex-col md:flex-row justify-between items-center gap-8">
        <p class="text-[10px] font-bold text-zinc-300 uppercase tracking-[0.4em]">Curator Credentials Verified • Operational Parameters Locked</p>      
        <button wire:click="save" wire:loading.attr="disabled" class="bg-black text-white px-12 py-5 rounded-full font-bold text-[11px] uppercase tracking-[0.3em] shadow-2xl hover:scale-105 transition-all duration-500 ambient-shadow">
            <span wire:loading.remove wire:target="save">Execute Global Update</span>
            <span wire:loading wire:target="save">Applying Configurations...</span>
        </button>    
    </div>
</div>
