@php
    $shopName = \App\Models\Setting::get('shop_name', 'Car Showroom');
@endphp

@if($sidebar)
    <flux:sidebar.brand name="{{ $shopName }}" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-zinc-900 text-white p-1">
            <x-app-logo-icon class="size-6 fill-current" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="{{ $shopName }}" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-zinc-900 text-white p-1">
            <x-app-logo-icon class="size-6 fill-current" />
        </x-slot>
    </flux:brand>
@endif
