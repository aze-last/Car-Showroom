<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

@php
    $seoTitle = (isset($title) && $title) ? $title : config('app.name');
    $seoDescription = (isset($description) && $description) ? $description : 'Discover elite vintage and modern luxury cars at The Gallery. Participate in active timed auctions and view our certified showroom today.';
    $seoUrl = (isset($canonicalUrl) && $canonicalUrl) ? $canonicalUrl : request()->url();
    $seoImage = (isset($metaImage) && $metaImage) ? $metaImage : asset('favicon.svg');
@endphp

<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}" />
<link rel="canonical" href="{{ $seoUrl }}" />

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website" />
<meta property="og:title" content="{{ $seoTitle }}" />
<meta property="og:description" content="{{ $seoDescription }}" />
<meta property="og:url" content="{{ $seoUrl }}" />
<meta property="og:image" content="{{ $seoImage }}" />

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $seoTitle }}" />
<meta name="twitter:description" content="{{ $seoDescription }}" />
<meta name="twitter:image" content="{{ $seoImage }}" />

@stack('styles')

@php
    $dynamicLogo = \App\Models\Setting::get('design_logo_path') ?: \App\Models\Setting::get('shop_logo');
    $logoUrl = $dynamicLogo ? Storage::url($dynamicLogo) : '/favicon.svg';
@endphp

<link rel="icon" href="{{ $logoUrl }}" sizes="any">
<link rel="icon" href="{{ $logoUrl }}" type="image/svg+xml">
<link rel="apple-touch-icon" href="{{ $logoUrl }}">

<link rel="preconnect" href="https://fonts.bunny.net">
<!-- Leaflet Interactive Maps -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<style>
    .leaflet-container {
        background: #e5e7eb !important;
        width: 100% !important;
        height: 100% !important;
    }
    .leaflet-tile-container img {
        max-width: none !important;
        max-height: none !important;
    }
</style>



@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
