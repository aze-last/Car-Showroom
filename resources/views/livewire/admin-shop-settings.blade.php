<x-pages::settings.layout :heading="__('Shop Information')" :subheading="__('Manage your showroom public details and social media.')">
    <div class="space-y-6">
        <form wire:submit="save" class="space-y-6">
            <div class="space-y-4">
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Shop Branding</p>
                <div class="flex items-center gap-6">
                    @if ($logo instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile && $logo->isPreviewable())
                        <img src="{{ $logo->temporaryUrl() }}" class="size-20 rounded-2xl object-cover border border-zinc-100">
                    @elseif ($current_logo_url)
                        <img src="{{ $current_logo_url }}" class="size-20 rounded-2xl object-cover border border-zinc-100">
                    @else
                        <div class="flex size-20 items-center justify-center rounded-2xl bg-zinc-50 border border-zinc-100">
                            <svg viewBox="0 0 24 24" fill="none" class="h-8 w-8 text-zinc-300" stroke="currentColor" stroke-width="1.5">
                                <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    @endif
                    
                    <div class="flex-1">
                        <flux:input type="file" wire:model="logo" label="Change Logo" />
                        <p class="mt-2 text-[10px] text-zinc-400">PNG, JPG or SVG. Max 1MB.</p>
                    </div>
                </div>
            </div>

            <flux:input wire:model="shop_name" label="Shop Name" placeholder="Car Showroom" />
            
            <flux:textarea wire:model="shop_address" label="Address" placeholder="123 Showroom St..." rows="2" />

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="shop_email" label="Public Email" placeholder="contact@example.com" />
                <flux:input wire:model="shop_phone" label="Contact Number" placeholder="+63..." />
            </div>

            <div class="space-y-4 pt-4 border-t border-zinc-50">
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Location Settings (Leaflet Map)</p>
                <div class="grid grid-cols-2 gap-4">
                    <flux:input wire:model="map_latitude" label="Latitude" placeholder="14.5995" />
                    <flux:input wire:model="map_longitude" label="Longitude" placeholder="120.9842" />
                </div>
                <p class="text-[10px] text-zinc-400 italic">Enter the geographic coordinates for your showroom's location on the map.</p>
            </div>

            <div class="space-y-4 pt-4 border-t border-zinc-50">
                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Social Media Links</p>
                
                <flux:input wire:model="facebook_url" label="Facebook URL">
                    <x-slot name="icon">
                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    </x-slot>
                </flux:input>

                <flux:input wire:model="instagram_url" label="Instagram URL">
                    <x-slot name="icon">
                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    </x-slot>
                </flux:input>

                <flux:input wire:model="tiktok_url" label="TikTok URL">
                    <x-slot name="icon">
                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/></svg>
                    </x-slot>
                </flux:input>
            </div>

            <div class="flex justify-end pt-4">
                <flux:button type="submit" variant="primary" class="w-full sm:w-auto" wire:loading.attr="disabled">
                    <flux:icon.arrow-path wire:loading class="mr-2 h-4 w-4 animate-spin" />
                    Save Shop Information
                </flux:button>
            </div>
        </form>
    </div>
</x-pages::settings.layout>
