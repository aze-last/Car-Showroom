<div x-data="{ 
    scrollY: 0,
    handleScroll() { this.scrollY = window.scrollY }
}" @scroll.window="handleScroll">
    
    <!-- Render Preset Layout -->
    @include('livewire.public.presets.' . $designLayout)

    <!-- Global Feature: Featured Auction Spotlight (Optional) -->
    @if($designSettings['showAuctions'])
        <livewire:public.auction-spotlight />
    @endif

</div>
